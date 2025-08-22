<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimerTracker extends Model
{
    use HasFactory;
    protected $fillable = [
        'tender_id',
        'user_id',
        'stage',
        'start_time',
        'end_time',
        'duration_hours',
        'status',
        'remaining_time'
    ];

    protected $dates = ['start_time', 'end_time'];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
