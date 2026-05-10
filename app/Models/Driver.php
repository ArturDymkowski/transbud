<?php

namespace App\Models;

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
        'region',
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
}
