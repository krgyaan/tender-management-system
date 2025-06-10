<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocketSlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'phydoc_id',
        'file'
    ];

    public function phydocslips()
    {
        return $this->belongsTo(PhyDocs::class, 'phydoc_id');
    }
}
