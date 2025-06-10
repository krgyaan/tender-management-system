<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'tender_id',
        'requirement',
        'unit',
        'qty',
    ];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class);
    }
}
