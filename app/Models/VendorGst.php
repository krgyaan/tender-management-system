<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorGst extends Model
{
    use HasFactory;
    protected $fillable = [
        'org',
        'gst_state',
        'gst_num',
    ];
}
