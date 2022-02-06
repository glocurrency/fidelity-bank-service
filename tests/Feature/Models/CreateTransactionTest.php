<?php

namespace GloCurrency\FidelityBank\Tests\Feature\Models;

use Illuminate\Support\Facades\Event;
use GloCurrency\FidelityBank\Tests\FeatureTestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Models\Sender;
use GloCurrency\FidelityBank\Models\Recipient;
use GloCurrency\FidelityBank\Events\TransactionCreatedEvent;

class CreateTransactionTest extends FeatureTestCase
{
    /** @test */
    public function fire_event_when_it_created(): void
    {
        Event::fake();

        Transaction::factory()->create();

        Event::assertDispatched(TransactionCreatedEvent::class);
    }

    /** @test */
    public function it_cannot_be_created_with_the_same_transaction_id()
    {
        Transaction::factory()->create([
            'transaction_id' => 'trx-1',
        ]);

        try {
            Transaction::factory()->create([
                'transaction_id' => 'trx-1',
            ]);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(\PDOException::class, $th);
            $this->assertCount(1, Transaction::where([
                'transaction_id' => 'trx-1',
            ])->get());
            return;
        }

        $this->fail('Exception was not thrown');
    }

    /** @test */
    public function it_cannot_be_created_with_the_same_fidelity_sender_id()
    {
        $sender = Sender::factory()->create();

        Transaction::factory()->create([
            'fidelity_sender_id' => $sender->id,
        ]);

        try {
            Transaction::factory()->create([
                'fidelity_sender_id' => $sender->id,
            ]);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(\PDOException::class, $th);
            $this->assertCount(1, Transaction::where([
                'fidelity_sender_id' => $sender->id,
            ])->get());
            return;
        }

        $this->fail('Exception was not thrown');
    }

    /** @test */
    public function it_cannot_be_created_with_the_same_fidelity_recipient_id()
    {
        $recipient = Recipient::factory()->create();

        Transaction::factory()->create([
            'fidelity_recipient_id' => $recipient->id,
        ]);

        try {
            Transaction::factory()->create([
                'fidelity_recipient_id' => $recipient->id,
            ]);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(\PDOException::class, $th);
            $this->assertCount(1, Transaction::where([
                'fidelity_recipient_id' => $recipient->id,
            ])->get());
            return;
        }

        $this->fail('Exception was not thrown');
    }

    /** @test */
    public function it_cannot_be_created_with_the_same_reference()
    {
        Event::fake([
            TransactionCreatedEvent::class,
        ]);

        Transaction::factory()->create([
            'reference' => '1234',
        ]);

        try {
            Transaction::factory()->create([
                'reference' => '1234',
            ]);
        } catch (\Throwable $th) {
            $this->assertInstanceOf(\PDOException::class, $th);
            $this->assertCount(1, Transaction::where('reference', '1234')->get());
            return;
        }

        $this->fail('Exception was not thrown');
    }
}
