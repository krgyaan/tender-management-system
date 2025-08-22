<?php

namespace App\Models\Gst3B;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gst3B extends Model
{
    use HasFactory;

    protected $table = 'gst3b';

    protected $fillable = [
        'period',
        'return_type',
        'tally_data_link',
        'gst_2a_file_path',
        'gst_tds_file_path',
        'gst_tds_accepted',
        'gst_tds_amount',
        'gst_paid',
        'payment_challan_path',
        'amount',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'gst_tds_accepted' => 'boolean',
        'gst_paid' => 'boolean',
    ];

    // Add relationships if needed
}
