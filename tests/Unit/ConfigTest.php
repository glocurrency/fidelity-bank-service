<?php

namespace GloCurrency\FidelityBank\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Config;
use BrokeYourBike\FidelityBank\Interfaces\ConfigInterface;

class ConfigTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function it_implemets_config_interface(): void
    {
        $this->assertInstanceOf(ConfigInterface::class, new Config());
    }

    /** @test */
    public function it_will_return_empty_string_if_value_not_found()
    {
        $configPrefix = 'services.fidelity_bank.api';

        // config is empty
        config([$configPrefix => []]);

        $config = new Config();

        $this->assertSame('', $config->getUrl());
        $this->assertSame('', $config->getUsername());
        $this->assertSame('', $config->getPassword());
    }

    /** @test */
    public function it_can_return_values()
    {
        $url = $this->faker->url();
        $username = $this->faker->userName();
        $password = $this->faker->password();

        $configPrefix = 'services.fidelity_bank.api';

        config(["{$configPrefix}.url" => $url]);
        config(["{$configPrefix}.username" => $username]);
        config(["{$configPrefix}.password" => $password]);

        $config = new Config();

        $this->assertSame($url, $config->getUrl());
        $this->assertSame($username, $config->getUsername());
        $this->assertSame($password, $config->getPassword());
    }
}
