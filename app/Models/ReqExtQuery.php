<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqExtQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'req_ext_id',
        'page_no',
        'clause_no',
        'query_type',
        'current_statement',
        'requested_statement'
    ];

    public function extension()
    {
        return $this->belongsTo(ReqExt::class, 'req_ext_id');
    }
}
