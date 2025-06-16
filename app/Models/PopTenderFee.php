<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopTenderFee extends Model
{
    use HasFactory;
    protected $fillable = [
        'tender_id',
        'emd_id',
        'tender_name',
        'due_date_time',
        'purpose',
        'portal_name',
        'netbanking_available',
        'bank_debit_card',
        'amount',
        'status',
        'reason',
        'utr',
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
}
