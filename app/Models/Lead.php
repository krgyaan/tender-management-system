<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'company_name',
        'name',
        'designation',
        'phone',
        'email',
        'address',
        'state',
        'type',
        'industry',
        'team',
        'points_discussed',
        've_responsibility'
    ];
}
