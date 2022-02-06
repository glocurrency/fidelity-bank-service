<?php

namespace GloCurrency\FidelityBank\Jobs;

use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Bus\Queueable;
use GloCurrency\MiddlewareBlocks\Enums\QueueTypeEnum as MQueueTypeEnum;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Exceptions\SendTransactionException;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;
use GloCurrency\FidelityBank\Enums\ErrorCodeFactory;
use BrokeYourBike\FidelityBank\Enums\StatusCodeEnum;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;
use BrokeYourBike\FidelityBank\Client;

class SendTransactionJob implements ShouldQueue, ShouldBeUnique, ShouldBeEncrypted
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    private Transaction $targetTransaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $targetTransaction)
    {
        $this->targetTransaction = $targetTransaction;
        $this->afterCommit();
        $this->onQueue(MQueueTypeEnum::SERVICES->value);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->targetTransaction->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (TransactionStateCodeEnum::LOCAL_UNPROCESSED !== $this->targetTransaction->state_code) {
            throw SendTransactionException::stateNotAllowed($this->targetTransaction);
        }

        try {
            /** @var Client */
            $api = App::make(Client::class);
            $response = $api->sendTransaction($this->targetTransaction);
        } catch (\Throwable $e) {
            report($e);
            throw SendTransactionException::apiRequestException($e);
        }

        $errorCode = ErrorCodeEnum::tryFrom($response->responseCode);
        $statusCode = StatusCodeEnum::tryFrom($response->status);

        if (!$errorCode) {
            throw SendTransactionException::unexpectedErrorCode($response->responseCode);
        }

        if (!$statusCode) {
            throw SendTransactionException::unexpectedStatusCode($response->status);
        }

        $this->targetTransaction->error_code = $errorCode;
        $this->targetTransaction->status_code = $statusCode;
        $this->targetTransaction->state_code = ErrorCodeFactory::getTransactionStateCode($errorCode);
        $this->targetTransaction->error_code_description = $response->responseMessage;
        $this->targetTransaction->save();

        if (TransactionStateCodeEnum::PROCESSING === $this->targetTransaction->state_code) {
            FetchTransactionUpdateJob::dispatch($this->targetTransaction);
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        report($exception);

        if ($exception instanceof SendTransactionException) {
            $this->targetTransaction->update([
                'state_code' => $exception->getStateCode(),
                'state_code_reason' => $exception->getStateCodeReason(),
            ]);
            return;
        }

        $this->targetTransaction->update([
            'state_code' => TransactionStateCodeEnum::LOCAL_EXCEPTION,
            'state_code_reason' => $exception->getMessage(),
        ]);
    }
}
