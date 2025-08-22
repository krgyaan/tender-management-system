<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountChecklistReport extends Model
{
    protected $fillable = [
        'checklist_id',
        'responsible_user_id',
        'accountable_user_id',
        'type',
        'due_date',
        'resp_completed_at',
        'acc_completed_at',
        'resp_remark',
        'resp_result_file',
        'acc_remark',
        'acc_result_file',
        'resp_timer',
        'acc_timer'
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
