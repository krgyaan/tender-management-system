<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtTenderFee extends Model
{
    use HasFactory;
    protected $fillable = [
        'tender_id',
        'emd_id',
        'type',
        'tender_name',
        'due_date',
        'purpose',
        'account_name',
        'account_number',
        'ifsc',
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
