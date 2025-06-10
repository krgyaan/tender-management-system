<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkEligible extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'info_id',
        'worktype',
        'value',
        'availablity',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class);
    }

    public function info()
    {
        return $this->belongsTo(TenderInformation::class);
    }
}
