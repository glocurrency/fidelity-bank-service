<?php

namespace GloCurrency\FidelityBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\FidelityBank\Tests\Fixtures\ProcessingItemFixture;
use GloCurrency\FidelityBank\Tests\FeatureTestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Events\TransactionCreatedEvent;

class GetProcessingItemTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_processing_item(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $processingItem = ProcessingItemFixture::factory()->create();

        $transaction = Transaction::factory()->create([
            'processing_item_id' => $processingItem->id,
        ]);

        $this->assertSame($processingItem->id, $transaction->fresh()->processingItem->id);
    }
}
