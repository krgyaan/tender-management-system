<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_googleapikey extends Model
{
    protected $fillable = [
        'staffid',
        'driveid',
        'title',
        'type',
        'description',
        'tenderid',
        'final_price',
        'receipt',
        'budget',
        'gross_margin',
        'oem',
    ];
}
