<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basic_detail extends Model
{
    protected $fillable = [
        'tender_name_id',
        'number',
        'date',
        'par_gst',
        'par_amt',
        'image',
        'ip',
        'strtotime',
    ];

    public function tenderName()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_name_id', 'id');
    }

    // Relationship with WoDetails
    public function wo_details()
    {
        return $this->hasOne(Wodetails::class, 'basic_detail_id', 'id');
    }

    // Relationship with Wo_acceptance_yes
    public function wo_acceptance_yes()
    {
        return $this->hasOne(Wo_acceptance_yes::class, 'basic_detail_id', 'id');
    }
}
