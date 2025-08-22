<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateCostingSheet extends Model
{
    protected $fillable = [
        'enquiry_id',
        'title',
        'sheet_url',
        'prepared_by',
        'final_price',
        'receipt_pre_gst',
        'budget_pre_gst',
        'gross_margin',
        'documents',
        'remarks',
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }
}
