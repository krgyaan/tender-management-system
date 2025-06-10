<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatteryAccessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'battery_id',
        'item_id',
        'acc_name',
        'price',
        'total',
    ];

    public function battery()
    {
        return $this->belongsTo(BatterySheet::class, 'battery_id');
    }

    public function batteryItem()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
