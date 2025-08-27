<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfq extends Model
{
    use HasFactory;

    protected $table = 'rfqs';
    protected $fillable = [
        'tender_id',
        'team_name',
        'organisation',
        'location',
        'item_name',
        'docs_list',
        'due_date'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }

    public function rfqVendors()
    {
        return $this->hasMany(RfqVendor::class, 'rfq_id', 'id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'rfq_vendors', 'rfq_id', 'vendor_id');
    }

    public function technicals()
    {
        return $this->hasMany(RfqTechnical::class, 'rfq_id', 'id');
    }

    public function scopes()
    {
        return $this->hasMany(RfqScope::class, 'rfq_id', 'id');
    }
    public function boqs()
    {
        return $this->hasMany(RfqBoq::class, 'rfq_id', 'id');
    }

    public function mafs()
    {
        return $this->hasMany(RfqMaf::class, 'rfq_id', 'id');
    }
    public function miis()
    {
        return $this->hasMany(RfqMii::class, 'rfq_id', 'id');
    }

    public function requirementss()
    {
        return $this->hasMany(RfqItem::class, 'rfq_id', 'id');
    }

    public function rfqResponse()
    {
        return $this->hasOne(RfqResponse::class, 'rfq_id', 'id');
    }

    public function rfqItems()
    {
        return $this->hasMany(RfqItem::class, 'rfq_id', 'id');
    }
}
