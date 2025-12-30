<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobCard extends Model
{
    protected $fillable = [
        'company_id',
        'customer_id',
        'job_card_no',
        'job_description',
        'priority',
        'status',
        'job_date',
        'scheduled_date',
        'completion_date',
        'estimated_hours',
        'actual_hours',
        'notes',
        'created_by',
        'invoice_no',
        'model_no',
        'serial_no',
        'service_attend',
        'service_attend_mobile',
        'loading_hr',
        'service_start_time',
        'service_end_time',
        'reference_no',
        'job_report_date',
        'job_report_no',
        'service_remarks',
        'customer_remarks'
    ];

    protected $casts = [
        'job_date' => 'date',
        'scheduled_date' => 'date',
        'completion_date' => 'date',
        'job_report_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(JobCardPart::class);
    }
}