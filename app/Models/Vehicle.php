<?php

namespace App\Models;

use App\Enums\VehicleTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'registration_number',
        'vin',
        'type',
        'technical_inspection_expiry_date',
        'insurance_expiry_date',
        'tachograph_inspection_expiry_date',
        'additional_notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type' => VehicleTypeEnum::class,
    ];

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $search = trim($search);
            $q->where(function ($q) use ($search) {
                $q->orWhere('registration_number', 'like', '%' . $search . '%')
                    ->orWhere('vin', 'like', '%' . $search . '%');
            });
        });
    }
}
