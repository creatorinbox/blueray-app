<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'company_id',
        'customer_name',
        'customer_username',
        'designation',
        'trn',
        'phone',
        'mobile',
        'email',
        'alt_email',
        'gstin',
        'tax_number',
        'credit_limit',
        'opening_balance',
        'custom_period',
        'payment_terms_days',
        'country',
        'state',
        'city',
        'postcode',
        'address',
        'customer_notes',
        'is_active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'is_active' => 'boolean'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }
}
