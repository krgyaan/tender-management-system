<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmdBg extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'bg_favour',
        'bg_address',
        'bg_expiry',
        'bg_amt',
        'bg_cont_percent',
        'bg_fdr_percent',
        'bg_needs',
        'bg_stamp',
        'bg_courier_addr',
        'bg_format_doer',
        'bg_format_imran',
        'bg_po',
        'bg_client_user',
        'bg_client_cp',
        'bg_client_fin',
        'bg_bank_name',
        'bg_bank_acc',
        'bg_bank_ifsc',
        'bg_status',
        'bg_claim',
        'bg_rejection',
        'courier_no',
    ];

    public function emds()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }
    public function courier() {
        return $this->belongsTo(CourierDashboard::class,'courier_no');
    }
}
