<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractor extends Model
{
    /** @use HasFactory<\Database\Factories\ContractorFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'active',
        'name',
        'nip',
        'regon',
        'phone',
        'email',
        'notes',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(ContractorAddress::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $search = trim($search);
            $q->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%')
                    ->orWhere('regon', 'like', '%' . $search . '%');
            });
        });
    }
}
