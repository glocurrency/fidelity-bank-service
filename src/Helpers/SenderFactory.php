<?php

namespace GloCurrency\FidelityBank\Helpers;

use GloCurrency\MiddlewareBlocks\Contracts\SenderInterface as MSenderInterface;
use GloCurrency\FidelityBank\Models\Sender;

class SenderFactory
{
    public static function makeFrom(MSenderInterface $from): Sender
    {
        return new Sender([
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
