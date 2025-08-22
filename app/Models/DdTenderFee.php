<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DdTenderFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'emd_id',
        'tender_name',
        'due_date_time',
        'dd_needed_in',
        'purpose_of_dd',
        'in_favour_of',
        'dd_payable_at',
        'dd_amount',
        'courier_address',
        'delivery_date_time',
        'status',
        'reason',
        'dd_no',
        'utr_msg',
        'remark',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }

    public function emd()
    {
        return $this->hasOne(Emds::class, 'tender_id', 'tender_id');
    }

    public function chqRec()
    {
        return $this->hasOne(EmdCheque::class, 'dd_id', 'id');
    }
}
