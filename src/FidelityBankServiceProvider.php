<?php

namespace GloCurrency\FidelityBank;

use Illuminate\Support\ServiceProvider;
use GloCurrency\FidelityBank\Config;
use BrokeYourBike\FidelityBank\Interfaces\ConfigInterface;

class FidelityBankServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMigrations();
        $this->registerPublishing();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->bindConfig();
    }

    /**
     * Setup the configuration for FidelityBank.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fidelity_bank.php', 'services.fidelity_bank'
        );
    }

    /**
     * Bind the FidelityBank logger interface to the FidelityBank logger.
     *
     * @return void
     */
    protected function bindConfig()
    {
        $this->app->bind(ConfigInterface::class, Config::class);
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (FidelityBank::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fidelity_bank.php' => $this->app->configPath('fidelity_bank.php'),
            ], 'fidelity-bank-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], 'fidelity-bank-migrations');
        }
    }
}
