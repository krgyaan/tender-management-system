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
