<?php

namespace App\Models\Accounts\Amc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmcSite extends Model
{
    protected $fillable = [
        'amc_id',
        'name',
        'address',
        'map_link'
    ];

    public function amc(): BelongsTo
    {
        return $this->belongsTo(Amc::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(AmcSiteContact::class);
    }
}