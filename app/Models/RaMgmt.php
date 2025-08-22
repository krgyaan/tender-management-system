<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaMgmt extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_no',
        'bid_submission_date',
        'status',
        'technically_qualified',
        'disqualification_reason',
        'qualified_parties',
        'start_time',
        'end_time',
        'start_price',
        'close_price',
        'close_time',
        'result',
        've_start_of_ra',
        'screenshot_qualified_parties',
        'screenshot_decrements',
        'final_result_screenshot'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_no', 'tender_no');
    }
}
