<?php

namespace GloCurrency\FidelityBank\Tests\Unit\Jobs;

use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\FidelityBank\Enums\StatusCodeEnum;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;
use BrokeYourBike\BaseModels\BaseUuid;

class TransactionTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Transaction::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Transaction::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_implemets_source_model_interface(): void
    {
        $this->assertInstanceOf(SourceModelInterface::class, new Transaction());
    }

    /** @test */
    public function it_returns_send_amount_as_float(): void
    {
        $transaction = new Transaction();
        $transaction->send_amount = '1.02';

        $this->assertSame(1.02, $transaction->send_amount);
    }

    /** @test */
    public function it_returns_receive_amount_as_float(): void
    {
        $transaction = new Transaction();
        $transaction->receive_amount = '1.02';

        $this->assertSame(1.02, $transaction->receive_amount);
    }

    /** @test */
    public function it_returns_state_code_as_enum(): void
    {
        $transaction = new Transaction();
        $transaction->setRawAttributes([
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED->value,
        ]);

        $this->assertEquals(TransactionStateCodeEnum::LOCAL_UNPROCESSED, $transaction->state_code);
    }

    /** @test */
    public function it_returns_error_code_as_enum(): void
    {
        $transaction = new Transaction();
        $transaction->setRawAttributes([
            'error_code' => ErrorCodeEnum::SYSTEM_EXCEPTION->value,
        ]);

        $this->assertEquals(ErrorCodeEnum::SYSTEM_EXCEPTION, $transaction->error_code);
    }

    /** @test */
    public function it_returns_status_code_as_enum(): void
    {
        $transaction = new Transaction();
        $transaction->setRawAttributes([
            'status_code' => StatusCodeEnum::PAID->value,
        ]);

        $this->assertEquals(StatusCodeEnum::PAID, $transaction->status_code);
    }
}
