<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgIndustry extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status'
    ];
}
