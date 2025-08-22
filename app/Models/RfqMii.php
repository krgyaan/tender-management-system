<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqMii extends Model
{
    use HasFactory;
    protected $fillable = [
        'rfq_id',
        'tender_id',
        'name',
        'file_path',
    ];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }
}
