<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'technically_qualified',
        'disqualification_reason',
        'qualified_parties_count',
        'qualified_parties_names',
        'result',
        'l1_price',
        'l2_price',
        'our_price',
        'qualified_parties_screenshot',
        'final_result'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id');
    }
}
