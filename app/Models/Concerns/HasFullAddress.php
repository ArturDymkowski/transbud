<?php

namespace App\Models\Concerns;

use App\Enums\CountriesEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasFullAddress
{
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = [];

                // Ulica nr_domu/nr_mieszkania
                $streetPart = trim($this->street ?? '');
                if ($this->house_nr) {
                    $streetPart .= ' ' . $this->house_nr;
                    if ($this->apartment_nr) {
                        $streetPart .= '/' . $this->apartment_nr;
                    }
                }
                if ($streetPart) {
                    $parts[] = $streetPart . '<br>';
                }

                // Kod pocztowy miasto
                $cityPart = '';
                if ($this->zipcode) {
                    $cityPart = $this->zipcode;
                }
                if ($this->city) {
                    $cityPart .= ($cityPart ? ' ' : '') . $this->city;
                }
                if ($cityPart) {
                    $parts[] = $cityPart;
                }

                // Kraj
                if ($this->country) {
                    if (!empty($parts)) {
                        $parts[] = ', ' . CountriesEnum::fromId($this->country->value)->label();
                    } else {
                        $parts[] = CountriesEnum::fromId($this->country->value)->label();
                    }
                }

                if (empty($parts)) {
                    return '-';
                }

                return implode('', $parts);
            }
        );
    }
}
