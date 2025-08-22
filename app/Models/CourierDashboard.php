<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierDashboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'to_org',
        'to_name',
        'to_addr',
        'to_pin',
        'to_mobile',
        'emp_from',
        'del_date',
        'urgency',
        'courier_docs',
        'courier_provider',
        'pickup_date',
        'docket_no',
        'docket_slip',
        'status',
        'delivery_date',
        'delivery_pod',
        'within_time',
        'status',
        'rej_remarks',
    ];

    public function courier_from()
    {
        return $this->belongsTo(User::class, 'emp_from', 'id');
    }

    public function timers()
    {
        return $this->hasMany(TimerTracker::class);
    }

    public function getTimer($stage = null)
    {
        $query = $this->hasOne(TimerTracker::class, 'tender_id')->where('stage', '=', 'courier_created')
            ->where('status', 'running');

        if ($stage) {
            $query->where('stage', $stage);
        }

        return $query->first();
    }

    public function remainedTime($stage = null)
    {
        $timer = $this->hasOne(TimerTracker::class, 'tender_id')->where('status', 'completed');
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
