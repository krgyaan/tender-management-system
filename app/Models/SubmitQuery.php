<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmitQuery extends Model
{
    use HasFactory;
    protected $table = 'submit_queries';
    protected $fillable = [
        'tender_id',
        'client_org',
        'client_name',
        'client_email',
        'client_phone',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id');
    }

    public function queryLists()
    {
        return $this->hasMany(SubmitQueryList::class, 'submit_queries_id');
    }
}
