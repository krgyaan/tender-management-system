<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wo_acceptance_yes extends Model
{
    protected $fillable = [
        'basic_detail_id',
        'page_no',
        'clause_no',
        'current_statement',
        'corrected_statement',
        'followup_frequency',
        'stop_opsans',
        'text',
        'image',
        'remark',
        'ip',
        'strtotime',
    ];

    public function basic_detail()
    {
        return $this->belongsTo(Basic_detail::class, 'basic_detail_id', 'id');
    }
}
