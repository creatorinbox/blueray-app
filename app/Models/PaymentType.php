<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $table = 'paymenttypes';
    protected $fillable = ['payment_type'];
    public $timestamps = false;
}
