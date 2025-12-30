<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturn extends Model
{
    protected $fillable = [
        'company_id', 'customer_id', 'sales_invoice_id', 'return_code', 'return_date', 'sales_code', 'without_vat', 'vat', 'invoice_total', 'paid_amount', 'due_amount', 'created_at', 'updated_at'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
