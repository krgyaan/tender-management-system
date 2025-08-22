<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisitContact extends Model
{
    protected $fillable = [
        'site_visit_id',
        'name',
        'designation',
        'phone',
        'email',
    ];

    public function siteVisit()
    {
        return $this->belongsTo(SiteVisit::class);
    }
}
