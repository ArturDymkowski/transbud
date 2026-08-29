<?php

namespace App\Models\Concerns;

use App\Enums\CountriesEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasFullAddress
{
    protected function fullAddressLines(): Attribute
    {
        return Attribute::make(
            get: function () {
                $lines = [];

                // Ulica nr_domu/nr_mieszkania
                $streetLine = trim($this->street ?? '');
                if ($this->house_nr) {
                    $streetLine .= ' '.$this->house_nr;
                    if ($this->apartment_nr) {
                        $streetLine .= '/'.$this->apartment_nr;
                    }
                }
                if ($streetLine !== '') {
                    $lines[] = $streetLine;
                }

                // Kod pocztowy miasto
                $cityLine = '';
                if ($this->zipcode) {
                    $cityLine = $this->zipcode;
                }
                if ($this->city) {
                    $cityLine .= ($cityLine ? ' ' : '').$this->city;
                }

                // Kraj — dopisywany do linii miasta, a jeśli jej nie ma, staje się nią.
                if ($this->country) {
                    $countryLabel = CountriesEnum::fromId($this->country->value)->label();
                    $cityLine = $cityLine !== '' ? $cityLine.', '.$countryLabel : $countryLabel;
                }

                if ($cityLine !== '') {
                    $lines[] = $cityLine;
                }

                return $lines;
            }
        );
    }

    protected function fullAddressText(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->fullAddressLines === [] ? '-' : implode(', ', $this->fullAddressLines)
        );
    }
}
