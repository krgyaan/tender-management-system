<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_id',
        'bid_submissions_date',
        'submitted_bid_documents',
        'proof_of_submission',
        'final_bidding_price',
        'missed_reason',
        'prevention_steps',
        'system_improvements',
        'status'
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInformation::class, 'tender_id');
    }

    public function tenderdue()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id');
    }
}
