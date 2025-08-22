<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateQuote extends Model
{
    protected $fillable = [
        'enquiry_id',
        'quote_submission_datetime',
        'submitted_documents',
        'contacts',
        'missed_reason',
        'oem_name',
        'prevent_repeat',
        'tms_improvement',
        'status'
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }
}
