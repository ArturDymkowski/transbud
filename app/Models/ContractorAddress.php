<?php

namespace App\Models;

use App\Enums\CountriesEnum;
use App\Models\Concerns\HasFullAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorAddress extends Model
{
    /** @use HasFactory<\Database\Factories\ContractorAddressFactory> */
    use HasFactory, SoftDeletes, HasFullAddress;

    protected $fillable = [
        'contractor_id',
        'country',
        'zipcode',
        'city',
        'street',
        'house_nr',
        'apartment_nr',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'country' => CountriesEnum::class,
    ];

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $search = trim($search);
            $q->where(function ($q) use ($search) {
                $q->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('street', 'like', '%' . $search . '%')
                    ->orWhere('zipcode', 'like', '%' . $search . '%')
                    ->orWhere('house_nr', 'like', '%' . $search . '%')
                    ->orWhere('apartment_nr', 'like', '%' . $search . '%')
                    ->orWhereHas('contractor', fn ($q2) => $q2->where('name', 'like', '%' . $search . '%'));
            });
        });
    }
}
