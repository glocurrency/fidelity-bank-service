<?php

namespace GloCurrency\FidelityBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\FidelityBank\Tests\FeatureTestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Events\TransactionUpdatedEvent;

class UpdateTransactionTest extends FeatureTestCase
{
    /** @test */
    public function fire_event_when_it_updated(): void
    {
        $transaction = Transaction::factory()->create([
            'state_code_reason' => 'abc',
        ]);

        Event::fake();

        $transaction->state_code_reason = 'xyz';
        $transaction->save();

        Event::assertDispatched(TransactionUpdatedEvent::class);
    }
}
