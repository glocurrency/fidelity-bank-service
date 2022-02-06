<?php

namespace GloCurrency\FidelityBank\Tests\Unit\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use GloCurrency\MiddlewareBlocks\Enums\QueueTypeEnum as MQueueTypeEnum;
use GloCurrency\MiddlewareBlocks\Contracts\ProcessingItemInterface as MProcessingItemInterface;
use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Jobs\CreateBankTransactionJob;

class CreateBankTransactionJobTest extends TestCase
{
    /** @test */
    public function it_has_tries_defined(): void
    {
        /** @var MProcessingItemInterface */
        $processingItem = $this->getMockBuilder(MProcessingItemInterface::class)->getMock();

        $job = (new CreateBankTransactionJob($processingItem));
        $this->assertSame(1, $job->tries);
    }

    /** @test */
    public function it_has_dispatch_queue_specified()
    {
        /** @var MProcessingItemInterface */
        $processingItem = $this->getMockBuilder(MProcessingItemInterface::class)->getMock();

        $job = (new CreateBankTransactionJob($processingItem));
        $this->assertEquals(MQueueTypeEnum::SERVICES->value, $job->queue);
    }

    /** @test */
    public function it_implements_should_be_unique(): void
    {
        /** @var MProcessingItemInterface */
        $processingItem = $this->getMockBuilder(MProcessingItemInterface::class)->getMock();

        $job = (new CreateBankTransactionJob($processingItem));
        $this->assertInstanceOf(ShouldBeUnique::class, $job);
        $this->assertSame($processingItem->getId(), $job->uniqueId());
    }

    /** @test */
    public function it_implements_should_be_encrypted(): void
    {
        /** @var MProcessingItemInterface */
        $processingItem = $this->getMockBuilder(MProcessingItemInterface::class)->getMock();

        $job = (new CreateBankTransactionJob($processingItem));
        $this->assertInstanceOf(ShouldBeEncrypted::class, $job);
    }
}
