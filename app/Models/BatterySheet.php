<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatterySheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'sheet_type',
        'bg',
        'freight_per',
        'cash_margin',
        'gst_battery',
        'gst_ic',
        'gst_buyback',
    ];

    public function items()
    {
        return $this->hasMany(BatterySheetItem::class, 'battery_id', 'id');
    }

    public function tenderInfo()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id', 'id');
    }

    public function batteryAccessories()
    {
        return $this->hasMany(BatteryAccessory::class, 'battery_id', 'id');
    }

    public function batteryIcs()
    {
        return $this->hasMany(BatteryIc::class, 'battery_id', 'id');
    }
}
