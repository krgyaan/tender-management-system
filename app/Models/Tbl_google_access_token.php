<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_google_access_token extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'userid',
        'status',
        'ip',
        'created_at',
        'updated_at',
    ];
}
