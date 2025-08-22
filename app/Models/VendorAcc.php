<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAcc extends Model
{
    use HasFactory;

    protected $fillable = [
        'org',
        'account_name',
        'account_num',
        'account_ifsc',
    ];
}
