<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderClient extends Model
{
    use HasFactory;
    protected $fillable = [
        'tender_id',
        'client_name',
        'client_designation',
        'client_mobile',
        'client_email',
    ];
    protected $table = 'tender_clients';
    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id');
    }
}
