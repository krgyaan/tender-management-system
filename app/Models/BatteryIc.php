<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatteryIc extends Model
{
    use HasFactory;

    protected $fillable = [
        'battery_id',
        'item_id',
        'ic_name',
        'price',
        'bic',
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
