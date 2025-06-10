<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BtTenderFee extends Model
{
    use HasFactory;
    protected $fillable = [
        'tender_id',
        'purpose',
        'account_name',
        'account_number',
        'ifsc',
        'amount',
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
