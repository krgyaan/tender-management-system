<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'document_name',
        'document_path',
    ];
    
    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }
}
