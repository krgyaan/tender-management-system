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
    ];
}