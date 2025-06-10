<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmdDemandDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'dd_favour',
        'dd_amt',
        'dd_payable',
        'dd_needs',
        'dd_purpose',
        'courier_add',
        'courier_deadline',
        'req_no',
    ];

    public function emd()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }

    public function ddChq()
    {
        return $this->hasOne(EmdCheque::class, 'dd_id');
    }

    public function courier() {
        return $this->belongsTo(CourierDashboard::class,'req_no');
    }
}
