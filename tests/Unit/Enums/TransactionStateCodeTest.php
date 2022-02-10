<?php

namespace GloCurrency\FidelityBank\Tests\Unit\Enums;

use GloCurrency\MiddlewareBlocks\Enums\ProcessingItemStateCodeEnum as MProcessingItemStateCodeEnum;
use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;
use BrokeYourBike\FidelityBank\Enums\StatusCodeEnum;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;

class TransactionStateCodeTest extends TestCase
{
    /** @test */
    public function it_can_return_processing_item_state_code_from_all_values()
    {
        foreach (TransactionStateCodeEnum::cases() as $value) {
            $this->assertInstanceOf(MProcessingItemStateCodeEnum::class, $value->getProcessingItemStateCode());
        }
    }

    /** @test */
    public function it_can_return_transaction_state_code_from_error_code_values()
    {
        foreach (ErrorCodeEnum::cases() as $value) {
            $this->assertInstanceOf(TransactionStateCodeEnum::class, TransactionStateCodeEnum::makeFromErrorCode($value));
        }
    }

    /** @test */
    public function it_can_return_transaction_state_code_from_status_code_values()
    {
        foreach (StatusCodeEnum::cases() as $value) {
            $this->assertInstanceOf(TransactionStateCodeEnum::class, TransactionStateCodeEnum::makeFromStatusCode($value));
        }
    }
}
