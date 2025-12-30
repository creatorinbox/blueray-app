<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryNote extends Model
{
    protected $fillable = [
        'customer_id',
        'delivery_date',
        'reference_no',
        'subject',
        'delivery_status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'delivery_notes',
        'created_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'subtotal' => 'decimal:3',
        'tax_amount' => 'decimal:3',
        'discount_amount' => 'decimal:3',
        'total_amount' => 'decimal:3',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryNoteItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedDeliveryDateAttribute()
    {
        return $this->delivery_date ? $this->delivery_date->format('d-m-Y') : '';
    }

    public function getDeliveryNoteNumberAttribute()
    {
        return 'DN-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}