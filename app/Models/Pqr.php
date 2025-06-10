<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pqr extends Model
{
    protected $fillable = [
        'team_name',
        'project_name',
        'value',
        'item',
        'po_date',
        'uplode_po',
        'uplode_sap_gem_po',
        'uplode_completion',
        'performace_cretificate',
        'remarks',
        'ip',
        'strtotime',
    ];

    
}
