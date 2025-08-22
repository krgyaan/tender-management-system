<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EligibleDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'info_id',
        'doc_name',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class);
    }

    public function info()
    {
        return $this->belongsTo(TenderInformation::class);
    }

    public function docName()
    {
        return $this->belongsTo(Finance::class, 'doc_name', 'id');
    }
}
