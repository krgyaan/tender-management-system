<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wodetails extends Model
{
    protected $fillable = [
        'basic_detail_id',
        'name',
        'organization',
        'departments',
        'phone',
        'email',
        'designation',
        'par_gst',
        'max_ld',
        'ldstartdate',
        'maxlddate',
        'pbg_applicable_status',
        'contract_agreement_status',
        'ip',
        'strtotime',
        'file_applicable',
        'file_agreement',
    ];
    
    public function basic_detail()
    {
        return $this->belongsTo(Basic_detail::class, 'basic_detail_id', 'id');
    }
}
