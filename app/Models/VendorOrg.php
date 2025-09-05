<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorOrg extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'org');
    }

    public function accounts()
    {
        return $this->hasMany(VendorAcc::class, 'org');
    }

    public function gsts()
    {
        return $this->hasMany(VendorGst::class, 'org');
    }
    public function files()
    {
        return $this->hasMany(VendorFiles::class, 'vendor_id');
    }
}
