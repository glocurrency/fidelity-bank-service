<?php

namespace GloCurrency\FidelityBank\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\MiddlewareBlocks\Contracts\ModelWithStateCodeInterface as MModelWithStateCodeInterface;
use GloCurrency\FidelityBank\Models\Recipient;
use GloCurrency\FidelityBank\FidelityBank;
use GloCurrency\FidelityBank\Events\TransactionUpdatedEvent;
use GloCurrency\FidelityBank\Events\TransactionCreatedEvent;
use GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum;
use GloCurrency\FidelityBank\Database\Factories\TransactionFactory;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\FidelityBank\Interfaces\TransactionInterface;
use BrokeYourBike\FidelityBank\Interfaces\SenderInterface;
use BrokeYourBike\FidelityBank\Interfaces\RecipientInterface;
use BrokeYourBike\FidelityBank\Enums\StatusCodeEnum;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;
use BrokeYourBike\BaseModels\BaseUuid;

/**
 * GloCurrency\FidelityBank\Models\Transaction
 *
 * @property string $id
 * @property string $transaction_id
 * @property string $processing_item_id
 * @property string $fidelity_sender_id
 * @property string $fidelity_recipient_id
 * @property \GloCurrency\FidelityBank\Enums\TransactionStateCodeEnum $state_code
 * @property string|null $state_code_reason
 * @property \BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum|null $error_code
 * @property string|null $error_code_description
 * @property \BrokeYourBike\FidelityBank\Enums\StatusCodeEnum|null $status_code
 * @property string|null $status_code_description
 * @property string $reference
 * @property int $request_postfix
 * @property string $send_currency_code
 * @property float $send_amount
 * @property string $receive_currency_code
 * @property float $receive_amount
 * @property string $bank_code
 * @property string $account_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Transaction extends BaseUuid implements MModelWithStateCodeInterface, SourceModelInterface, TransactionInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'fidelity_transactions';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<mixed>
     */
    protected $casts = [
        'state_code' => TransactionStateCodeEnum::class,
        'error_code' => ErrorCodeEnum::class,
        'status_code' => StatusCodeEnum::class,
        'send_amount' => 'double',
        'receive_amount' => 'double',
    ];

    /**
     * @var array<mixed>
     */
    protected $dispatchesEvents = [
        'created' => TransactionCreatedEvent::class,
        'updated' => TransactionUpdatedEvent::class,
    ];

    public function getStateCode(): TransactionStateCodeEnum
    {
        return $this->state_code;
    }

    public function getStateCodeReason(): ?string
    {
        return $this->state_code_reason;
    }

    public function getSender(): ?SenderInterface
    {
        return $this->sender;
    }

    public function getRecipient(): ?RecipientInterface
    {
        return $this->recipient;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getRequestSuffix(): int
    {
        return $this->request_postfix;
    }

    public function getDate(): \Carbon\CarbonInterface
    {
        return $this->created_at ?? now();
    }

    public function getSendCurrencyCode(): string
    {
        return $this->send_currency_code;
    }

    public function getReceiveCurrencyCode(): string
    {
        return $this->receive_currency_code;
    }

    public function getSendAmount(): float
    {
        return $this->send_amount;
    }

    public function getReceiveAmount(): float
    {
        return $this->receive_amount;
    }

    public function getAccountNumber(): string
    {
        return $this->account_number;
    }

    public function getBankCode(): string
    {
        return $this->bank_code;
    }

    /**
     * The ProcessingItem that Transaction belong to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function processingItem()
    {
        return $this->belongsTo(FidelityBank::$processingItemModel, 'processing_item_id', 'id');
    }

    /**
     * The Recipient that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recipient()
    {
        return $this->hasOne(Recipient::class, 'id', 'fidelity_recipient_id');
    }

    /**
     * The Sender that Transaction has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender()
    {
        return $this->hasOne(Sender::class, 'id', 'fidelity_sender_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TransactionFactory::new();
    }
}
