<?php

namespace App\Models\Accounts\Tds;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdsPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tds_id',
        'section',
        'amount',
        'utr_message',
        'payment_date'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function tds()
    {
        return $this->belongsTo(Tds::class);
    }
}