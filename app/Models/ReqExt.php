<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqExt extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'days',
        'reason',
        'client_org',
        'client_name',
        'client_email',
        'client_phone'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id');
    }
}
