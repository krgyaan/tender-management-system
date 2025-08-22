<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'receipt_datetime',
        'gst_percentage',
        'gst_type',
        'delivery_time',
        'freight_type',
        'quotation_document',
        'technical_documents',
        'maf_document',
        'mii_document'
    ];

    protected $casts = [
        'receipt_datetime' => 'datetime',
        'gst_percentage' => 'decimal:2',
    ];

    // Relationship with items
    public function items()
    {
        return $this->hasMany(RfqResponseItem::class, 'quotation_receipt_id');
    }

    // Relationship with RFQ
    public function rfq()
    {
        return $this->belongsTo(Rfq::class, 'rfq_id');
    }
}
