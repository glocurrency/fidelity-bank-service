<?php

namespace GloCurrency\FidelityBank\Tests;

use Money\Formatter\DecimalMoneyFormatter;
use Money\Currencies\ISOCurrencies;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(DecimalMoneyFormatter::class, function () {
            return new DecimalMoneyFormatter(new ISOCurrencies());
        });
    }

    protected function defineDatabaseMigrations()
    {
        // we are using default user migration to store fixtures
        $this->loadLaravelMigrations();
    }
}
