<?php

namespace GloCurrency\FidelityBank\Tests\Feature\Jobs;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Bus;
use GloCurrency\FidelityBank\Tests\FeatureTestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Jobs\SendTransactionJob;
use GloCurrency\FidelityBank\Jobs\FetchTransactionUpdateJob;
use GloCurrency\FidelityBank\Exceptions\SendTransactionException;
use GloCurrency\FidelityBank\Events\TransactionUpdatedEvent;
use GloCurrency\FidelityBank\Events\TransactionCreatedEvent;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\FidelityBank\Enums\StatusCodeEnum;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;
use BrokeYourBike\FidelityBank\Client;

class SendTransactionJobTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([
            TransactionCreatedEvent::class,
            TransactionUpdatedEvent::class,
        ]);
    }

    /**
     * @test
     * @dataProvider transactionStatesProvider
     */
    public function it_will_throw_if_state_not_LOCAL_UNPROCESSED(TransactionStateCodeEnum $stateCode, bool $shouldFail): void
    {
        /** @var Transaction */
        $fidelityTransaction = Transaction::factory()->create([
            'state_code' => $stateCode,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "Pin": "' . $fidelityTransaction->transaction_id . '",
            "AccountNumber": "' . $this->faker->bankAccountNumber() . '",
            "Status": "' . StatusCodeEnum::TRANSMIT->value . '",
            "ResponseCode": "' . ErrorCodeEnum::PAID->value . '",
            "ResponseMessage": "Request In Progress"
        }'));

        if ($shouldFail) {
            $this->expectExceptionMessage("Transaction state_code `{$fidelityTransaction->state_code->value}` not allowed");
            $this->expectException(SendTransactionException::class);
        }

        SendTransactionJob::dispatchSync($fidelityTransaction);

        if (!$shouldFail) {
            $this->assertEquals(TransactionStateCodeEnum::PAID, $fidelityTransaction->fresh()->state_code);
        }
    }

    public function transactionStatesProvider(): array
    {
        $states = collect(TransactionStateCodeEnum::cases())
            ->filter(fn($c) => !in_array($c, [
                TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            ]))
            ->flatten()
            ->map(fn($c) => [$c, true])
            ->toArray();

        $states[] = [TransactionStateCodeEnum::LOCAL_UNPROCESSED, false];

        return $states;
    }

    /** @test */
    public function it_will_throw_if_response_code_is_unexpected(): void
    {
        /** @var Transaction */
        $fidelityTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "ResponseCode": "' . ErrorCodeEnum::NOT_PERMITTED->value . '",
            "ResponseMessage": "",
            "Status": "not a code you can expect"
        }'));

        try {
            SendTransactionJob::dispatchSync($fidelityTransaction);
        } catch (\Throwable $th) {
            $this->assertEquals('Unexpected ' . StatusCodeEnum::class . ': `not a code you can expect`', $th->getMessage());
            $this->assertInstanceOf(SendTransactionException::class, $th);
        }

        /** @var Transaction */
        $fidelityTransaction = $fidelityTransaction->fresh();

        $this->assertEquals(TransactionStateCodeEnum::UNEXPECTED_STATUS_CODE, $fidelityTransaction->state_code);
    }

    /** @test */
    public function it_can_send_transaction(): void
    {
        Bus::fake([
            FetchTransactionUpdateJob::class,
        ]);

        /** @var Transaction */
        $fidelityTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "Pin": "' . $fidelityTransaction->transaction_id . '",
            "AccountNumber": "' . $this->faker->bankAccountNumber() . '",
            "Status": "' . StatusCodeEnum::TRANSMIT->value . '",
            "ResponseCode": "' . ErrorCodeEnum::PAID->value . '",
            "ResponseMessage": "Request In Progress"
        }'));

        SendTransactionJob::dispatchSync($fidelityTransaction);

        /** @var Transaction */
        $fidelityTransaction = $fidelityTransaction->fresh();

        $this->assertEquals(TransactionStateCodeEnum::PAID, $fidelityTransaction->state_code);
        $this->assertEquals(StatusCodeEnum::TRANSMIT, $fidelityTransaction->status_code);
        $this->assertEquals(ErrorCodeEnum::PAID, $fidelityTransaction->error_code);
        $this->assertSame('Request In Progress', $fidelityTransaction->error_code_description);
    }

    /**
     * @test
     * @dataProvider errorCodesProvider
     * */
    public function it_will_dispatch_fetch_job_when_error_code(ErrorCodeEnum $errorCode, int $shouldBeDispatchedTimes)
    {
        Bus::fake([
            FetchTransactionUpdateJob::class,
        ]);

        /** @var Transaction */
        $fidelityTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "Pin": "' . $fidelityTransaction->transaction_id . '",
            "AccountNumber": "' . $this->faker->bankAccountNumber() . '",
            "Status": "' . StatusCodeEnum::TRANSMIT->value . '",
            "ResponseCode": "' . $errorCode->value . '",
            "ResponseMessage": "Request In Progress"
        }'));

        SendTransactionJob::dispatchSync($fidelityTransaction);

        Bus::assertDispatchedTimes(FetchTransactionUpdateJob::class, $shouldBeDispatchedTimes);
    }

    public function errorCodesProvider(): array
    {
        $states = collect(ErrorCodeEnum::cases())
            ->filter(fn($c) => !in_array($c, [
                ErrorCodeEnum::IN_PROGRESS,
            ]))
            ->flatten()
            ->map(fn($c) => [$c, 0])
            ->toArray();

        $states[] = [ErrorCodeEnum::IN_PROGRESS, 1];

        return $states;
    }
}
