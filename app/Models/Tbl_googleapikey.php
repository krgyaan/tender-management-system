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
        'tenderid'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tenderid');
    }
}
