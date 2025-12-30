<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalLog extends Model
{
    protected $fillable = [
        'module_name',
        'record_id',
        'action',
        'action_by',
        'action_date',
        'remarks',
        'old_data',
        'new_data',
        'ip_address'
    ];

    protected $casts = [
        'action_date' => 'datetime',
        'old_data' => 'array',
        'new_data' => 'array'
    ];

    public function actionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by');
    }

    public static function logAction($module, $recordId, $action, $userId, $remarks = null, $oldData = null, $newData = null)
    {
        return self::create([
            'module_name' => $module,
            'record_id' => $recordId,
            'action' => $action,
            'action_by' => $userId,
            'action_date' => now(),
            'remarks' => $remarks,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip()
        ]);
    }
}
