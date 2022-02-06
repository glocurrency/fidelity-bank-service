<?php

namespace GloCurrency\FidelityBank\Helpers;

use GloCurrency\MiddlewareBlocks\Contracts\RecipientInterface as MRecipientInterface;
use GloCurrency\FidelityBank\Models\Recipient;

class RecipientFactory
{
    public static function makeFrom(MRecipientInterface $from): Recipient
    {
        return new Recipient([
            'first_name' => $from->getFirstName(),
            'last_name' => $from->getLastName(),
            'street' => $from->getStreet(),
            'city' => $from->getCity(),
            'postal_code' => $from->getPostalCode(),
            'phone_number' => $from->getPhoneNumber(),
            'country_code' => $from->getCountryCode(),
        ]);
    }
}
