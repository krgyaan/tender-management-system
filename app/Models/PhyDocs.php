<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhyDocs extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'client_name',
        'client_email',
        'client_phone',
        'courier_no',
        'docs',
        'slips',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Documents::class, 'id', 'phydoc_id');
    }

    public function docketslips()
    {
        return $this->hasMany(DocketSlip::class, 'id', 'phydoc_id');
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
