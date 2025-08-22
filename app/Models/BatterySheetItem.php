<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatterySheetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'battery_id',
        'item_name',
        'model',
        'ah',
        'cells',
        'spare_cell',
        'price',
        'banks',
    ];

    public function battery()
    {
        return $this->belongsTo(BatterySheet::class, 'battery_id', 'id');
    }

    public function accessories()
    {
        return $this->hasMany(BatteryAccessory::class, 'item_id', 'id');
    }

    public function ics()
    {
        return $this->hasMany(BatteryIc::class, 'item_id', 'id');
    }
}
