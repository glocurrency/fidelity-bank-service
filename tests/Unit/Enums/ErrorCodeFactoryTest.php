<?php

namespace GloCurrency\FidelityBank\Tests\Unit\Enums;

use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;
use GloCurrency\FidelityBank\Enums\ErrorCodeFactory;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;

class ErrorCodeFactoryTest extends TestCase
{
    /** @test */
    public function it_can_return_transaction_state_code_from_all_values()
    {
        foreach (ErrorCodeEnum::cases() as $value) {
            $this->assertInstanceOf(TransactionStateCodeEnum::class, ErrorCodeFactory::getTransactionStateCode($value));
        }
    }
}
