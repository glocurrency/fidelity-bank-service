<?php

namespace GloCurrency\FidelityBank\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use GloCurrency\FidelityBank\Tests\Fixtures\TransactionFixture;
use GloCurrency\FidelityBank\Tests\Fixtures\ProcessingItemFixture;
use GloCurrency\FidelityBank\FidelityBankServiceProvider;
use GloCurrency\FidelityBank\FidelityBank;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        FidelityBank::useTransactionModel(TransactionFixture::class);
        FidelityBank::useProcessingItemModel(ProcessingItemFixture::class);
    }

    protected function getPackageProviders($app)
    {
        return [FidelityBankServiceProvider::class];
    }

    /**
     * Create the HTTP mock for API.
     *
     * @return array<\GuzzleHttp\Handler\MockHandler|\GuzzleHttp\HandlerStack> [$httpMock, $handlerStack]
     */
    protected function mockApiFor(string $class): array
    {
        $httpMock = new \GuzzleHttp\Handler\MockHandler();
        $handlerStack = \GuzzleHttp\HandlerStack::create($httpMock);

        $this->app->when($class)
            ->needs(\GuzzleHttp\ClientInterface::class)
            ->give(function () use ($handlerStack) {
                return new \GuzzleHttp\Client(['handler' => $handlerStack]);
            });

        return [$httpMock, $handlerStack];
    }
}
