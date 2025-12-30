<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends Model
{
    protected $fillable = [
        'company_id',
        'customer_id',
        'quotation_id',
        'invoice_no',
        'invoice_date',
        'delivery_status',
        'delivery_date',
        'delivery_notes',
        'subtotal',
        'discount_to_all_input',
        'discount_to_all_type',
        'other_charges_input',
        'total_amount'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:3',
        'discount_to_all_input' => 'decimal:3',
        'other_charges_input' => 'decimal:3',
        'total_amount' => 'decimal:3',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}