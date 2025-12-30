<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    protected $fillable = [
        'sales_invoice_id',
        'item_id',
        'lot_id',
        'qty',
        'sale_price',
        'amount',
        'vat_amount',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
}
