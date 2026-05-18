<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'pesel',

        'country',
        'zipcode',
        'city',
        'street',
        'street_nr',
        'home_nr',
        'extra_info',

        'driving_license_number',
        'license_expiry_date',
        'medical_exam_valid_until',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $search = trim($search);
                $q->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('pesel', 'like', '%' . $search . '%')
                    ->orWhere('region', 'like', '%' . $search . '%')
                    ->orWhere('zipcode', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('street', 'like', '%' . $search . '%');
            });
        });
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = [];

                // Ulica nr_domu/nr_mieszkania
                $streetPart = trim($this->street ?? '');
                if ($this->street_nr) {
                    $streetPart .= ' ' . $this->street_nr;
                    if ($this->home_nr) {
                        $streetPart .= '/' . $this->home_nr;
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
                    $parts[] = ', ' . $this->country;
                }

                if (empty($parts)) {
                    return '-';
                }

                return implode('', $parts);
            }
        );
    }
}
