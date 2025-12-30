<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'company_id',
        'customer_id',
        'quotation_no',
        'quotation_date',
        'valid_till',
        'status',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'vat_amount',
        'total_amount',
        'terms_conditions',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_till' => 'date',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Status badge for UI
    public function getStatusBadgeAttribute()
    {
        return match($this->approval_status) {
            'Draft' => 'bg-secondary',
            'Submitted' => 'bg-warning',
            'Approved' => 'bg-success',
            'Rejected' => 'bg-danger',
            default => 'bg-light'
        };
    }
}
