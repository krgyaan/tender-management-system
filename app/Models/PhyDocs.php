<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhyDocs extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'courier_no',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }

    public function courier()
    {
        return $this->belongsTo(CourierDashboard::class, 'courier_no', 'id');
    }

    public function persons()
    {
        return $this->hasMany(PhydocsPerson::class, 'phydoc_id', 'id');
    }
}
