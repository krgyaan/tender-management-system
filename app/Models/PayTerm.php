<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'payment_terms',
        'pbg',
        'pbg_duration',
        'bid_valid',
        'comm_eval',
        'maf_req',
        'delivery',
        'supply',
        'installation',
        'total',
        'ldperweek',
        'maxld',
        'phyDocs'
    ];

    public function tenderInfo()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }

}
