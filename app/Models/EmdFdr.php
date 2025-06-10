<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmdFdr extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'fdr_purpose',
        'fdr_favour',
        'fdr_amt',
        'fdr_expiry',
        'fdr_needs',
        'fdr_bank_name',
        'fdr_bank_acc',
        'fdr_bank_ifsc',
        'fdr_status',
        'fdr_rejection',
    ];

    public function emds()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }
}
