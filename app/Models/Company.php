<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'base_currency',
        'vat_trn',
        'vat_rate',
        'financial_year_start',
        'financial_year_end',
        'is_active'
    ];

    protected $casts = [
        'vat_rate' => 'decimal:2',
        'financial_year_start' => 'date',
        'financial_year_end' => 'date',
        'is_active' => 'boolean'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
