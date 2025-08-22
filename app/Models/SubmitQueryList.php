<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmitQueryList extends Model
{
    use HasFactory;

    protected $table = 'submit_queries_lists';

    protected $fillable = [
        'submit_queries_id',
        'page_no',
        'clause_no',
        'query_type',
        'current_statement',
        'requested_statement'
    ];

    public function submitQuery()
    {
        return $this->belongsTo(SubmitQuery::class, 'submit_queries_id');
    }
}
