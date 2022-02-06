<?php

namespace GloCurrency\FidelityBank\Traits;

trait PersonTrait
{
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getMiddleName(): string
    {
        return '';
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getAddress(): ?string
    {
        return $this->street;
    }

    public function getState(): ?string
    {
        return $this->region;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function getCountryCode(): string
    {
        return $this->country_code_alpha2;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }
}
