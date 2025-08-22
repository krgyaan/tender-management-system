<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'doc_path',
    ];

    public function tenderInfo()
    {
        return $this->belongsTo(TenderInfo::class);
    }
}
