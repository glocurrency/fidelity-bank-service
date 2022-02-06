<?php

namespace GloCurrency\FidelityBank\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use GloCurrency\FidelityBank\Models\Transaction;
use GloCurrency\FidelityBank\Models\Sender;
use GloCurrency\FidelityBank\Models\Recipient;
use GloCurrency\FidelityBank\FidelityBank;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string,mixed>
     */
    public function definition()
    {
        $transactionModel = FidelityBank::$transactionModel;
        $processingItemModel = FidelityBank::$processingItemModel;

        return [
            'id' => $this->faker->uuid(),
            'transaction_id' => $transactionModel::factory(),
            'processing_item_id' => $processingItemModel::factory(),
            'fidelity_sender_id' => Sender::factory(),
            'fidelity_recipient_id' => Recipient::factory(),
            'state_code' => TransactionStateCodeEnum::LOCAL_UNPROCESSED,
            'reference' => $this->faker->uuid(),
            'request_postfix' => $this->faker->numberBetween(1, 9999),
            'send_currency_code' => $this->faker->currencyCode(),
            'send_amount' => $this->faker->randomFloat(2, 1),
            'receive_currency_code' => $this->faker->currencyCode(),
            'receive_amount' => $this->faker->randomFloat(2, 1),
            'bank_code' => $this->faker->unique()->word(),
            'account_number' => $this->faker->numerify('##########'),
        ];
    }
}
