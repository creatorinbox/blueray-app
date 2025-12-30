<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'supplier_id',
        'company_id',
        'po_no',
        'po_date',
        'currency',
        'currency_value',
        'status',
        'total_amount',
        'total_amount_omr',
        'remarks',
    ];

    protected $casts = [
        'po_date' => 'date',
        'total_amount' => 'decimal:2',
        'total_amount_omr' => 'decimal:3',
        'currency_value' => 'decimal:3',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }
}
