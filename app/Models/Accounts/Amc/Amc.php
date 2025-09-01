<?php

namespace App\Models\Accounts\Amc;

use App\Models\Project;
use App\Models\Accounts\Amc\AmcSite;
use App\Models\Accounts\Amc\AmcSiteContact;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Amc extends Model
{
    use SoftDeletes;

    protected $fillable = ['team_name', 
                            'project_id', 
                            'service_frequency', 
                            'amc_start_date', 
                            'amc_end_date', 
                            'bill_frequency', 
                            'bill_type', 
                            'bill_value', 
                            'variable_bills', 
                            'amc_po_path',
                            'service_report_path',
                            'signed_service_report_path',
                        ];

    protected $casts = [
        'amc_start_date' => 'date',
        'amc_end_date' => 'date',
        'variable_bills' => 'array', // Automatic JSON casting
    ];

    /**
     * Relationships
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(AmcSite::class);
    }

    public function engineers(): HasMany
    {
        return $this->hasMany(AmcServiceEngineer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(AmcProduct::class);
    }

    /**
     * Accessors
     */
    public function getAmcPoUrlAttribute()
    {
        return $this->amc_po_path ? Storage::url($this->amc_po_path) : null;
    }

    /**
     * Get the total value of all variable bills
     */
    public function getVariableBillsTotalAttribute()
    {
        if (!$this->variable_bills) {
            return 0;
        }

        return array_reduce(
            $this->variable_bills,
            function ($carry, $bill) {
                return $carry + ($bill['amount'] ?? 0);
            },
            0,
        );
    }
    public function contacts()
    {
        return $this->hasManyThrough(
            AmcSiteContact::class, // Final model (contacts)
            AmcSite::class, // Intermediate model (sites)
            'amc_id', // Foreign key on amc_sites table
            'amc_site_id', // Foreign key on amc_site_contacts table
            'id', // Local key on amcs table
            'id', // Local key on amc_sites table
        );
    }
    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('amc_end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('amc_end_date', '<', now());
    }

    /**
     * Helper method to add a variable bill
     */
    public function addVariableBill($date, $amount)
    {
        $bills = $this->variable_bills ?? [];
        $bills[] = [
            'date' => $date,
            'amount' => $amount,
        ];
        $this->variable_bills = $bills;
    }

    public function getServiceReportUrlAttribute()
    {
        return $this->service_report_path ? Storage::url('amc/service_reports/' . $this->service_report_path) : null;
    }

    public function getSignedServiceReportUrlAttribute()
    {
        return $this->signed_service_report_path ? Storage::url('amc/signed_service_reports/' . $this->signed_service_report_path) : null;
    }
}