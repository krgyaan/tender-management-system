<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'info_id',
        'wo_name',
    ];

    public function info()
    {
        return $this->belongsTo(TenderInformation::class, 'info_id');
    }

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id');
    }

    public function woName()
    {
        return $this->belongsTo(Pqr::class, 'wo_name', 'id');
    }
}
