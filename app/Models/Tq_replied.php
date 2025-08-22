<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tq_replied extends Model
{
    protected $fillable = [
        'tender_id',
        'date',
        'time',
        'tq_img',
        'proof_submission',
    ];
}
