<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'is_rejectable',
        'reject_reason',
        'reject_remarks',
        'tender_fees',
        'emd_req',
        'emd_opt',
        'rev_auction',
        'pt_supply',
        'pt_ic',
        'pbg',
        'pbg_duration',
        'bid_valid',
        'comm_eval',
        'maf_req',
        'supply',
        'installation',
        'ldperweek',
        'maxld',
        'phyDocs',
        'dead_date',
        'dead_time',
        'tech_eligible',
        'order1',
        'order2',
        'order3',
        'aat',
        'aat_amt',
        'wc',
        'wc_amt',
        'sc',
        'sc_amt',
        'nw',
        'nw_amt',
        'pqr_eligible',
        'fin_eligible',
        'te_remark',
        'rej_remark'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }

    public function workOrder()
    {
        return $this->hasMany(WorkOrder::class, 'info_id', 'id');
    }

    public function eligibleDocs()
    {
        return $this->hasMany(EligibleDoc::class, 'info_id', 'id');
    }

    public function workEligible()
    {
        return $this->hasMany(WorkEligible::class, 'info_id', 'id');
    }

    public function ra()
    {
        return $this->hasOne(RaMgmt::class, 'tender_no', 'tender_id');
    }
}
