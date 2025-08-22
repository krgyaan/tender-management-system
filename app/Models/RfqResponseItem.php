<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqResponseItem extends Model
{
    use HasFactory;

    protected $table = 'quotation_receipt_items';

    protected $fillable = [
        'quotation_receipt_id',
        'item_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'amount'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    // Relationship with quotation receipt
    public function quotationReceipt()
    {
        return $this->belongsTo(RfqResponse::class, 'quotation_receipt_id');
    }

    // Relationship with item
    public function itemName()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}
