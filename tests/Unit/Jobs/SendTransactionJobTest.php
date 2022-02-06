<?php

namespace GloCurrency\FidelityBank\Tests\Unit\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use GloCurrency\MiddlewareBlocks\Enums\QueueTypeEnum as MQueueTypeEnum;
use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Jobs\SendTransactionJob;

class SendTransactionJobTest extends TestCase
{
    /** @test */
    public function it_has_tries_defined(): void
    {
        $transaction = new Transaction();

        $job = (new SendTransactionJob($transaction));
        $this->assertSame(1, $job->tries);
    }

    /** @test */
    public function it_implements_should_be_unique(): void
    {
        $transaction = new Transaction();

        $job = (new SendTransactionJob($transaction));
        $this->assertInstanceOf(ShouldBeUnique::class, $job);
        $this->assertSame($transaction->id, $job->uniqueId());
    }

    /** @test */
    public function it_has_dispatch_queue_specified()
    {
        $transaction = new Transaction();

        $job = (new SendTransactionJob($transaction));
        $this->assertEquals(MQueueTypeEnum::SERVICES->value, $job->queue);
    }

    /** @test */
    public function it_implements_should_be_encrypted(): void
    {
        $transaction = new Transaction();

        $job = (new SendTransactionJob($transaction));
        $this->assertInstanceOf(ShouldBeEncrypted::class, $job);
    }
}
