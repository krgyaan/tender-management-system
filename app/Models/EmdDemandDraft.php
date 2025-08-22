<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmdDemandDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'dd_favour',
        'dd_amt',
        'dd_payable',
        'dd_needs',
        'dd_purpose',
        'courier_add',
        'courier_deadline',
        'status',
        'req_no',
        'generated_dd'
    ];

    public function emd()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }

    public function ddChq()
    {
        return $this->hasOne(EmdCheque::class, 'dd_id');
    }

    public function courier() {
        return $this->belongsTo(CourierDashboard::class,'req_no');
    }
    
    public function timers()
    {
        return $this->hasMany(TimerTracker::class, 'tender_id', 'id');
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
        $timer = $this->hasOne(TimerTracker::class, 'tender_id')
            ->where('status', 'completed');
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
