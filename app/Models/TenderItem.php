<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'name'
    ];

    public function tenderInfo()
    {
        return $this->belongsTo(TenderInfo::class);
    }


    public function itemName()
    {
        return $this->belongsTo(Item::class, 'name', 'id');
    }
}
