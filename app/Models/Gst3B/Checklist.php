<?php
// File: app/Models/Gst3B/Checklist.php

namespace App\Models\Gst3B;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'frequency',
        'responsibility',
        'accountability',
        'description',
        'file_path',
        'file_original_name',
        'file_description',
        'responsibility_timer',
        'accountability_timer',
        'responsibility_remark',
        'responsibility_remark_date',
        'responsibility_remark_by',
        'accountability_remark',
        'accountability_remark_date',
        'accountability_remark_by',
        'upload_remark',
        'upload_remark_date',
        'upload_remark_by',
        'final_result_path',
        'responsibility_completed',
        'accountability_completed',
        'result_uploaded',
    ];

    // For Laravel 8+ use $casts instead of $dates
    protected $casts = [
        'responsibility_remark_date' => 'datetime',
        'accountability_remark_date' => 'datetime',
        'upload_remark_date' => 'datetime',
        'responsibility_completed_at' => 'datetime',
        'accountability_completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Eager load these relationships by default to prevent N+1 queries
    protected $with = [
        'responsibleUser',
        'accountableUser',
        'responsibilityRemarkBy',
        'accountabilityRemarkBy',
        'uploadRemarkBy'
    ];

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsibility');
    }

    public function accountableUser()
    {
        return $this->belongsTo(User::class, 'accountability');
    }

    public function responsibilityRemarkBy()
    {
        return $this->belongsTo(User::class, 'responsibility_remark_by');
    }

    public function accountabilityRemarkBy()
    {
        return $this->belongsTo(User::class, 'accountability_remark_by');
    }

    public function uploadRemarkBy()
    {
        return $this->belongsTo(User::class, 'upload_remark_by');
    }

    // Accessor to safely handle responsibility_remark_date
    public function getResponsibilityRemarkDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    // Accessor to safely handle accountability_remark_date
    public function getAccountabilityRemarkDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
}
