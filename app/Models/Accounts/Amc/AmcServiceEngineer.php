<?php

namespace App\Models\Accounts\Amc;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmcServiceEngineer extends Model
{
    protected $fillable = [
        'amc_id',
        'name',
        'organization',
        'mobile',
        'email'
    ];

    public function amc(): BelongsTo
    {
        return $this->belongsTo(Amc::class);
    }
}