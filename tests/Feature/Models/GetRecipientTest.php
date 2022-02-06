<?php

namespace GloCurrency\FidelityBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\FidelityBank\Tests\FeatureTestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Models\Recipient;
use GloCurrency\FidelityBank\Events\TransactionCreatedEvent;

class GetRecipientTest extends FeatureTestCase
{
    /** @test */
    public function it_can_get_recipient(): void
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        $recipient = Recipient::factory()->create();

        $transaction = Transaction::factory()->create([
            'fidelity_recipient_id' => $recipient->id,
        ]);

        $this->assertSame($recipient->id, $transaction->fresh()->recipient->id);
    }
}
