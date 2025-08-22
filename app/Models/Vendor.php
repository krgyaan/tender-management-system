<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'org',
        'name',
        'email',
        'mobile',
        'address'
    ];

    public function rfqVendors()
    {
        return $this->hasMany(RfqVendor::class);
    }

    public function vendorOrg()
    {
        return $this->belongsTo(VendorOrg::class, 'org');
    }

    public function rfqs()
    {
        return $this->belongsToMany(Rfq::class, 'rfq_vendors', 'vendor_id', 'rfq_id');
    }
}
