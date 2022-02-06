<?php

namespace GloCurrency\FidelityBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\FidelityBank\Tests\FeatureTestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Models\Sender;
use GloCurrency\FidelityBank\Events\TransactionCreatedEvent;

class GetSenderTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_sender(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $sender = Sender::factory()->create();

        $transaction = Transaction::factory()->create([
            'fidelity_sender_id' => $sender->id,
        ]);

        $this->assertSame($sender->id, $transaction->fresh()->sender->id);
    }
}
