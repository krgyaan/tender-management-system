<?php

namespace App\Models\Accounts\Amc;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Item;

class AmcProduct extends Model
{
    protected $fillable = [
        'amc_id',
        'item_id',
        'description',
        'make',
        'model',
        'serial_no',
        'quantity'
    ];

    public function amc(): BelongsTo
    {
        return $this->belongsTo(Amc::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}