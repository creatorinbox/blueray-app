<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmcService extends Model
{
    protected $fillable = [
        'amc_no',
        'customer_id',
        'service_item',
        'start_date',
        'end_date',
        'amc_type',
        'contract_value',
        'vat',
        'invoice_ref',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_value' => 'decimal:2',
        'vat' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
