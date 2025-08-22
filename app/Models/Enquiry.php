<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'organisation_id',
        'item_id',
        'location_code',
        'approx_value',
        'site_visit_required',
        'document_path',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'site_visit_required' => 'boolean',
        'approx_value' => 'decimal:2',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organization::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_code', 'acronym');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function siteVisits()
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function costingSheets()
    {
        return $this->hasOne(PrivateCostingSheet::class);
    }

    public function privateQuote()
    {
        return $this->hasOne(PrivateQuote::class);
    }
}
