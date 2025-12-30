<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'grn_id',
        'payment_date',
        'amount',
        'payment_type',
        'paid_status',
        'paid_type',
        'cheque_no',
        'cheque_date',
        'payment_note',
        'credit_due',
        'supplier_id',
    ];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }
}
