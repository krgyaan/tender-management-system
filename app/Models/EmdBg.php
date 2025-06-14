<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmdBg extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'bg_favour',
        'bg_address',
        'bg_expiry',
        'bg_amt',
        'bg_cont_percent',
        'bg_fdr_percent',
        'bg_needs',
        'bg_stamp',
        'bg_courier_addr',
        'bg_format_doer',
        'bg_format_imran',
        'bg_po',
        'bg_client_user',
        'bg_client_cp',
        'bg_client_fin',
        'bg_bank_name',
        'bg_bank_acc',
        'bg_bank_ifsc',
        'bg_status',
        'bg_claim',
        'bg_rejection',
        'courier_no',
        'prefilled_signed_bg'
    ];

    public function emds()
    {
        return $this->belongsTo(Emds::class, 'emd_id');
    }
    
    public function courier() {
        return $this->belongsTo(CourierDashboard::class,'courier_no');
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
