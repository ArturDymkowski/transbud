<?php

namespace App\Models;

use App\Enums\CountriesEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Driver extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    public const MEDIA_DRIVING_LICENSE_FRONT = 'driving_license_front';
    public const MEDIA_DRIVING_LICENSE_BACK  = 'driving_license_back';
    public const MEDIA_IDENTITY_CARD_FRONT   = 'identity_card_front';
    public const MEDIA_IDENTITY_CARD_BACK    = 'identity_card_back';

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
        'driving_license_expiry_date',
        'identity_card_number',
        'identity_card_expiry_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'country' => CountriesEnum::class,
    ];

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $search = trim($search);
                $q->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('pesel', 'like', '%' . $search . '%')
                    ->orWhere('zipcode', 'like', '%' . $search . '%')
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
                    $parts[] = ', ' . CountriesEnum::fromId($this->country->value)->label();
                }

                if (empty($parts)) {
                    return '-';
                }

                return implode('', $parts);
            }
        );
    }

    public function registerMediaCollections(): void
    {
        $collections = [
            self::MEDIA_DRIVING_LICENSE_FRONT,
            self::MEDIA_DRIVING_LICENSE_BACK,
            self::MEDIA_IDENTITY_CARD_FRONT,
            self::MEDIA_IDENTITY_CARD_BACK,
        ];

        foreach ($collections as $collection) {
            $this->addMediaCollection($collection)
                ->useDisk('driver_documents')
                ->singleFile()
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(250)
            ->sharpen(10)
            ->performOnCollections(
                self::MEDIA_DRIVING_LICENSE_FRONT,
                self::MEDIA_DRIVING_LICENSE_BACK,
                self::MEDIA_IDENTITY_CARD_FRONT,
                self::MEDIA_IDENTITY_CARD_BACK,
            );
    }
}
