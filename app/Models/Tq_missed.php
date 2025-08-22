<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tq_missed extends Model
{
    protected $fillable = [
        'tender_id',
        'reason_missing',
        'would_repeated',
        'tms_system',
    ];
}
