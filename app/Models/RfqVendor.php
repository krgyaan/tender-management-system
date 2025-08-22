<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqVendor extends Model
{
    use HasFactory;

    protected $table = 'rfq_vendors';

    protected $fillable = [
        'rfq_id',
        'tender_id',
        'org',
        'vendor',
        'email',
        'mobile'
    ];

    public function rfq()
    {
        return $this->belongsTo(Rfq::class, 'rfq_id', 'id');
    }

    public function rfqVendors()
    {
        return $this->hasMany(RfqVendor::class, 'rfq_id', 'id');
    }

    public function vendorss()
    {
        return $this->belongsTo(Vendor::class, 'vendor', 'id');
    }

    public function organizations()
    {
        return $this->belongsTo(Organization::class, 'org', 'id');
    }
}
