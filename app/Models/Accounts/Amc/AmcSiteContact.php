<?php

namespace App\Models\Accounts\Amc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmcSiteContact extends Model
{
    protected $fillable = [
        'amc_site_id',
        'name',
        'organization',
        'mobile',
        'email'
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(AmcSite::class);
    }
}