<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'company_id',
        'item_name',
        'item_code',
        'item_type',
        'stock_type',
        'brand',
        'oem_part_no',
        'duplicate_part_no',
        'hsn_code',
        'unit',
        'min_quantity',
        'opening_stock',
        'barcode',
        'sale_price',
        'purchase_price',
        'currency',
        'currency_value',
        'profit_margin',
        'final_price',
        'min_sale_price',
        'description',
        'vat_applicable',
        'vat_rate',
        'is_active',
        'current_stock',
    ];

    protected $casts = [
        'sale_price' => 'decimal:3',
        'purchase_price' => 'decimal:3',
        'profit_margin' => 'decimal:2',
        'final_price' => 'decimal:3',
        'min_sale_price' => 'decimal:3',
        'vat_rate' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'opening_stock' => 'decimal:2',
        'vat_applicable' => 'boolean',
        'is_active' => 'boolean',
        'current_stock' => 'decimal:2',
        'currency_value' => 'decimal:3',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function stockLots(): HasMany
    {
        return $this->hasMany(StockLot::class);
    }
}
