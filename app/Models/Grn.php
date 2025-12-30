<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'supplier_id',
        'grn_no',
        'grn_date',
        'currency',
        'exchange_rate',
        'invoice_no',
        'remarks',
    ];

    protected $casts = [
        'grn_date' => 'date',
        'exchange_rate' => 'decimal:4',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }
    public function payments()
    {
        return $this->hasMany(GrnPayment::class);
    }
}
