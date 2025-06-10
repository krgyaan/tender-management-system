<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmdCheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'dd_id',
        'cheque_favour',
        'cheque_amt',
        'cheque_date',
        'cheque_needs',
        'cheque_reason',
        'cheque_bank',
        'status',
        'reason',
        'cheq_no',
        'duedate',
        'handover',
        'cheq_img',
        'confirmation',
        'remarks',
    ];


    public function emds()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }

    public function chqDd()
    {
        return $this->belongsTo(EmdDemandDraft::class, 'dd_id');
    }
}
