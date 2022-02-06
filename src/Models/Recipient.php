<?php

namespace GloCurrency\FidelityBank\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\FidelityBank\Traits\PersonTrait;
use GloCurrency\FidelityBank\Database\Factories\RecipientFactory;
use BrokeYourBike\FidelityBank\Interfaces\RecipientInterface;
use BrokeYourBike\CountryCasts\Alpha2Cast;
use BrokeYourBike\BaseModels\BaseUuid;

/**
 * GloCurrency\FidelityBank\Models\Recipient
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $street
 * @property string|null $region
 * @property string|null $city
 * @property string|null $postal_code
 * @property string $country_code
 * @property string $country_code_alpha2
 * @property string|null $phone_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Recipient extends BaseUuid implements RecipientInterface
{
    use HasFactory;
    use SoftDeletes;
    use PersonTrait;

    protected $table = 'fidelity_recipients';

    /**
     * @var array<mixed>
     */
    protected $casts = [
        'country_code_alpha2' => Alpha2Cast::class . ':country_code',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return RecipientFactory::new();
    }
}
