<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'enquiry_id',
        'assigned_to',
        'scheduled_at',
        'conducted_at',
        'information',
        'additional_notes',
        'documents',
        'status',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function contacts()
    {
        return $this->hasMany(SiteVisitContact::class);
    }
}
