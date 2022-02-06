<?php

namespace GloCurrency\FidelityBank\Tests\Feature\Jobs;

use Illuminate\Support\Facades\Event;
use GloCurrency\FidelityBank\Tests\FeatureTestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Jobs\FetchTransactionUpdateJob;
use GloCurrency\FidelityBank\Exceptions\FetchTransactionUpdateException;
use GloCurrency\FidelityBank\Events\TransactionCreatedEvent;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\FidelityBank\Enums\StatusCodeEnum;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;
use BrokeYourBike\FidelityBank\Client;

class FetchTransactionUpdateJobTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([
            TransactionCreatedEvent::class,
        ]);
    }

    /**
     * @test
     * @dataProvider transactionStatesProvider
     */
    public function it_will_throw_if_state_not_PROCESSING(TransactionStateCodeEnum $stateCode, bool $shouldFail): void
    {
        $targetTransaction = Transaction::factory()->create([
            'state_code' => $stateCode,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "Status": "' . StatusCodeEnum::TRANSMIT->value . '",
            "ResponseCode": "' . ErrorCodeEnum::IN_PROGRESS->value . '",
            "ResponseMessage": "Request In Progress"
        }'));

        try {
            FetchTransactionUpdateJob::dispatchSync($targetTransaction);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(FetchTransactionUpdateException::class, $th);
            $this->assertStringContainsString("Transaction state_code `{$targetTransaction->state_code->value}` not allowed", $th->getMessage());
            return;
        }

        if ($shouldFail) {
            $this->fail('Exception was not thrown');
        }

        $this->assertSame(0, $httpMock->count());
    }

    public function transactionStatesProvider(): array
    {
        $states = collect(TransactionStateCodeEnum::cases())
            ->filter(fn($c) => !in_array($c, [
                TransactionStateCodeEnum::PROCESSING,
            ]))
            ->flatten()
            ->map(fn($c) => [$c, true])
            ->toArray();

        $states[] = [TransactionStateCodeEnum::PROCESSING, false];

        return $states;
    }

    /** @test */
    public function it_can_update_state_code()
    {
        $targetTransaction = Transaction::factory()->create([
            'state_code' => TransactionStateCodeEnum::PROCESSING,
        ]);

        [$httpMock] = $this->mockApiFor(Client::class);
        $httpMock->append(new \GuzzleHttp\Psr7\Response(200, [], '{
            "Status": "' . StatusCodeEnum::TRANSMIT->value . '",
            "ResponseCode": "' . ErrorCodeEnum::PAID->value . '",
            "ResponseMessage": "Paid"
        }'));

        FetchTransactionUpdateJob::dispatchSync($targetTransaction);

        $targetTransaction = $targetTransaction->fresh();
        $this->assertInstanceOf(Transaction::class, $targetTransaction);

        $this->assertEquals(TransactionStateCodeEnum::PAID, $targetTransaction->state_code);
    }
}
