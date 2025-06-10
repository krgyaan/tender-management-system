<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'purpose',
        'bt_acc',
        'bt_ifsc',
        'bt_acc_name',
        'bt_amount',
        'status',
        'reason',
        'utr',
        'remarks'
    ];

    public function emd()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }
}
