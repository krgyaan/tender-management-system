<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerComplaint extends Model
{
    use HasFactory;

    protected $table = 'customer_complaints';
    
    protected $fillable = [
        'name',
        'organization',
        'designation',
        'phone',
        'email',
        'site_project_name',
        'po_no',
        'site_location',
        'attachment',
        'issue_faced',
        'status',
        'ticket_no'
    ];

    public function serviceEngineer()
    {
        return $this->hasOne(ServiceEngineer::class, 'complaint_id');
    }
    public function serviceReport()
    {
        return $this->hasOne(ServiceReport::class, 'complaint_id');
    }
    public function callDetails()
    {
        return $this->hasOne(ServiceConferenceCall::class, 'complaint_id');
    }
}