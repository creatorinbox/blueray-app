<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCardPart extends Model
{
    protected $fillable = [
        'job_card_id',
        'item_id',
        'lot_id',
        'qty_used',
        'cost_price',
        'sale_price',
        'amount'
    ];

    protected $casts = [
        'qty_used' => 'decimal:2',
        'cost_price' => 'decimal:4',
        'amount' => 'decimal:3',
    ];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

        public function lot()
        {
            return $this->belongsTo(StockLot::class, 'lot_id');
        }
}