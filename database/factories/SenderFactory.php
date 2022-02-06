<?php

namespace GloCurrency\FidelityBank\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\FidelityBank\Models\Sender;

class SenderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sender::class;

    /**
     * Define the model's default state.
     *
     * @return array<string,mixed>
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'street' => $this->faker->streetAddress(),
            'region' => $this->faker->word(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'country_code' => $this->faker->countryISOAlpha3(),
            'phone_number' => $this->faker->e164PhoneNumber(),
        ];
    }
}
