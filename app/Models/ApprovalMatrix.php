<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalMatrix extends Model
{
    protected $table = 'approval_matrix';
    
    protected $fillable = [
        'module_name',
        'role_required',
        'min_amount',
        'max_amount',
        'sequence_order',
        'conditions',
        'is_active'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'sequence_order' => 'integer',
        'conditions' => 'array',
        'is_active' => 'boolean'
    ];

    public static function getApprovalFlow($module, $amount = 0, $conditions = [])
    {
        return self::where('module_name', $module)
                  ->where('is_active', true)
                  ->where(function($query) use ($amount) {
                      $query->where('min_amount', '<=', $amount)
                           ->where(function($q) use ($amount) {
                               $q->where('max_amount', '>=', $amount)
                                 ->orWhereNull('max_amount');
                           });
                  })
                  ->orderBy('sequence_order')
                  ->get();
    }
}
