<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    protected $fillable = [
        'grn_id',
        'item_id',
        'lot_no',
        'expiry_date',
        'qty_received',
        'base_cost',
        'duty_amount',
        'freight_amount',
        'landed_cost_per_unit',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'qty_received' => 'decimal:2',
        'base_cost' => 'decimal:4',
        'duty_amount' => 'decimal:4',
        'freight_amount' => 'decimal:4',
        'landed_cost_per_unit' => 'decimal:4',
    ];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

        public function lot()
        {
            return $this->belongsTo(StockLot::class, 'lot_id');
        }
}
