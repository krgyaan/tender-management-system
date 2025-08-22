<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tq_received extends Model
{
    protected $fillable = [
        'tender_id',
        'tq_type',
        'description',
        'tq_submission_date',
        'tq_submission_time',
        'tq_img',
        'ip',
        'strtotime',
    ];
}
