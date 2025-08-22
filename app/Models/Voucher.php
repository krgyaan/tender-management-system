<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'name_id',
        'amount',
        'from',
        'to',
        'prepared_by',
        'acc_sign',
        'admin_sign',
        'admin_sign_date',
        'account_sign_date',
        'acc_remark',
        'admin_remark',
    ];
}
