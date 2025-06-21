<?php

namespace App\Models\Gst3B;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_name',
        'amount_type',
        'amount',
        'payment_method',
        'account_name',
        'account_number',
        'ifsc',
        'due_date',
        'frequency',
        'status',
        'utr_message',
        'payment_datetime',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_datetime' => 'datetime',
    ];
}
