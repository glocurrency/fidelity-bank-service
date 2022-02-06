<?php

namespace GloCurrency\FidelityBank\Tests\Unit\Enums;

use GloCurrency\MiddlewareBlocks\Enums\ProcessingItemStateCodeEnum as MProcessingItemStateCodeEnum;
use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;

class TransactionStateCodeTest extends TestCase
{
    /** @test */
    public function it_can_return_processing_item_state_code_from_all_values()
    {
        foreach (TransactionStateCodeEnum::cases() as $value) {
            $this->assertInstanceOf(MProcessingItemStateCodeEnum::class, $value->getProcessingItemStateCode());
        }
    }
}
