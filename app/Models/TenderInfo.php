<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TenderInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'team',
        'tender_no',
        'organisation',
        'tender_name',
        'item',
        'gst_values',
        'tender_fees',
        'emd',
        'team_member',
        'due_date',
        'due_time',
        'remarks',
        'status',
        'location',
        'website',
        'tlRemarks',
        'rfq_to',
        'oem_who_denied',
        'client_organisation',
        'courier_address',
    ];

    // Relationship with TenderItem
    public function itemName()
    {
        return $this->belongsTo(Item::class, 'item', 'id');
    }

    // Relationship with TenderDoc
    public function docs()
    {
        return $this->hasMany(TenderDoc::class, 'tender_id', 'id');
    }

    // Relationship with Status
    public function statuses()
    {
        return $this->belongsTo(Status::class, 'status', 'id');
    }

    // Relationship with User
    public function users()
    {
        return $this->hasOne(User::class, 'id', 'team_member');
    }

    // Relationship with organizations
    public function organizations()
    {
        return $this->hasOne(Organization::class, 'id', 'organisation');
    }

    // Relationship with PayTerm
    public function info()
    {
        return $this->hasOne(TenderInformation::class, 'tender_id', 'id');
    }

    // Relationship with WorkEligible
    public function workeligible()
    {
        return $this->hasMany(WorkEligible::class, 'tender_id', 'id');
    }

    // Relationship with Rfq
    public function rfqs()
    {
        return $this->hasOne(Rfq::class, 'tender_id', 'id');
    }

    // Relationship with PhyDocs
    public function phydocs()
    {
        return $this->hasOne(PhyDocs::class, 'tender_id', 'id');
    }

    // Relationship with EMD
    public function emds()
    {
        return $this->hasMany(Emds::class, 'tender_id', 'id');
    }

    // Relationship with Location
    public function locations()
    {
        return $this->belongsTo(Location::class, 'location', 'id');
    }

    // Relationship with Website
    public function websites()
    {
        return $this->belongsTo(Websites::class, 'website', 'id');
    }

    // Relationship with WorkOrder
    public function workorders()
    {
        return $this->hasMany(WorkOrder::class, 'tender_id', 'id');
    }

    // Relationship with EligibleDoc
    public function eligibleDocs()
    {
        return $this->hasMany(EligibleDoc::class, 'tender_id', 'id');
    }

    // Relationship with Vendor
    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'id', 'rfq_to');
    }

    // Relationship with BidSubmission
    public function bs()
    {
        return $this->hasOne(BidSubmission::class, 'tender_id', 'id');
    }

    // Relationship with Costing Sheet
    public function sheet()
    {
        return $this->hasOne(Tbl_googleapikey::class, 'tenderid', 'id');
    }

    // Relatiionship with result
    public function result()
    {
        return $this->hasOne(TenderResult::class, 'tender_id', 'id');
    }

    // Relationship with Client
    public function client()
    {
        return $this->hasMany(TenderClient::class, 'tender_id', 'id');
    }
    
    // Relationship with DocumentChecklist
    public function checklist()
    {
        return $this->hasMany(DocumentChecklist::class, 'tender_id', 'id');
    }

    // Relationship with TQ_received
    public function tq_received()
    {
        return $this->hasMany(Tq_received::class, 'tender_id', 'id');
    }

    // Relationship with TQ_replied
    public function tq_replied()
    {
        return $this->hasMany(Tq_replied::class, 'tender_id', 'id');
    }

    // Relationship with TQ_missed
    public function tq_missed()
    {
        return $this->hasMany(Tq_missed::class, 'tender_id', 'id');
    }
    
    // Relationship with RaMgmt
    public function ra_mgmt()
    {
        return $this->hasMany(RaMgmt::class, 'tender_no', 'id');
    }
    
    // Relationship with BasicDetails
    public function basic_details()
    {
        return $this->hasOne(Basic_detail::class, 'tender_name_id', 'id');
    }

    public function calculateTenderCompletion()
    {
        $fields = [
            'tender_no',
            'tender_name',
            'organisation',
            'gst_values',
            'tender_fees',
            'emd',
            'team_member',
            'due_date',
            'due_time',
            'status',
            'remarks',
            'location',
            'website',
        ];
        $totalFields = count($fields);
        $completedFields = 0;

        $completedFields = 0;
        foreach ($fields as $field) {
            if (!empty($this->{$field})) {
                $completedFields++;
            } else {
                Log::info("Field $field is empty for tender ID: '.$this->id.'");
            }
        }

        if ($this->docs()->count() > 0) {
            $completedFields++;
        } else {
            Log::info("Field docs is empty for tender ID: '.$this->id.'");
        }

        if ($this->phydocs()->count() > 0) {
            $completedFields++;
        } else {
            Log::info("Field phydoc is empty for tender ID: '.$this->id.'");
        }

        if ($this->info()->count() > 0) {
            $completedFields++;
        } else {
            Log::info("Field payterms is empty for tender ID: '.$this->id.'");
        }

        if ($this->emds()->count() > 0) {
            $completedFields++;
        } else {
            Log::info("Field emds is empty for tender ID: '.$this->id.'");
        }

        $completionPercentage = ($completedFields / ($totalFields + 5)) * 100;
        return round($completionPercentage, 2);
    }

    public function timers()
    {
        return $this->hasMany(TimerTracker::class);
    }

    public function getTimer($stage = null)
    {
        $query = $this->hasOne(TimerTracker::class, 'tender_id')
            ->where('status', 'running');

        if ($stage) {
            $query->where('stage', $stage);
        }

        return $query->first();
    }

    public function remainedTime($stage = null)
    {
        $timer = $this->hasOne(TimerTracker::class, 'tender_id')->where('status', 'completed');
        if ($timer) {
            if ($stage) {
                $timer->where('stage', $stage);
            }

            $timer = $timer->first();
            if ($timer) {
                if ($timer->remaining_time > 0)
                    $color = 'success';
                else
                    $color = 'danger';
            } else {
                return '<span class="badge bg-warning">NO TIMER</span>';
            }

            return $timer ? sprintf('<span class="badge bg-%s">%02d:%02d</span>', $color, $timer->remaining_time / 3600, ($timer->remaining_time / 60) % 60) : '';
        } else {
            return '<span class="badge bg-warning">NO TIMER</span>';
        }
    }
}
