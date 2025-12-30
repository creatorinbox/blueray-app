<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLot extends Model
{
    protected $fillable = [
        'item_id',
        'lot_no',
        'expiry_date',
        'qty_available',
        'cost_price',
        'is_active'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty_available' => 'decimal:2',
        'cost_price' => 'decimal:4',
        'is_active' => 'boolean'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
