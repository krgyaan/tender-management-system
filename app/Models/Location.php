<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'acronym',
        'state',
        'region',
        'status'
    ];

    public function tenders()
    {
        return $this->hasMany(TenderInfo::class, 'location', 'id');
    }
    
    public static $indianStatesAndUTs = [
        ''  => 'Select State/UT',
        1  => 'Andhra Pradesh',
        2  => 'Arunachal Pradesh',
        3  => 'Assam',
        4  => 'Bihar',
        5  => 'Chhattisgarh',
        6  => 'Goa',
        7  => 'Gujarat',
        8  => 'Haryana',
        9  => 'Himachal Pradesh',
        10 => 'Jharkhand',
        11 => 'Karnataka',
        12 => 'Kerala',
        13 => 'Madhya Pradesh',
        14 => 'Maharashtra',
        15 => 'Manipur',
        16 => 'Meghalaya',
        17 => 'Mizoram',
        18 => 'Nagaland',
        19 => 'Odisha',
        20 => 'Punjab',
        21 => 'Rajasthan',
        22 => 'Sikkim',
        23 => 'Tamil Nadu',
        24 => 'Telangana',
        25 => 'Tripura',
        26 => 'Uttar Pradesh',
        27 => 'Uttarakhand',
        28 => 'West Bengal',
        29 => 'Andaman and Nicobar Islands',
        30 => 'Chandigarh',
        31 => 'Dadra and Nagar Haveli and Daman and Diu',
        32 => 'Delhi',
        33 => 'Jammu and Kashmir',
        34 => 'Ladakh',
        35 => 'Lakshadweep',
        36 => 'Puducherry',
    ];

    public static $regions = [
        ''  => 'Select Region',
        1  => 'North',
        2  => 'South',
        3  => 'East',
        4  => 'West',
        5  => 'Central',
        6  => 'North East',
    ];
}
