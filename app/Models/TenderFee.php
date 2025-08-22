<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_no', 
        'tender_name', 
        'purpose', 
        'account_name', 
        'account_number', 
        'ifsc', 
        'amount'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_no', 'tender_no');
    }
}
    