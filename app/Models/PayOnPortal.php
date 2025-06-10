<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayOnPortal extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'purpose',
        'portal',
        'is_netbanking',
        'is_debit',
        'amount',
        'action',
        'status',
        'reason',
        'utr',
        'remarks',
        'utr_mgs',
        'transfer_date',
        'utr_num',
    ];

    public function emd()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }

    public function followups()
    {
        return $this->belongsTo(FollowUps::class, 'emd_id', 'id');
    }
}
