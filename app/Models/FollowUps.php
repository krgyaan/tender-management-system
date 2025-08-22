<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUps extends Model
{
    use HasFactory;

    protected $fillable = [
        'emd_id',
        'area',
        'party_name',
        'followup_for',
        'amount',
        'details',
        'assigned_to',
        'frequency',
        'stop_reason',
        'proof_text',
        'proof_img',
        'stop_rem',
        'latest_comment',
        'created_by',
        'assign_initiate',
        'start_from',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function followPerson()
    {
        return $this->hasMany(FollowUpPersons::class, 'follwup_id', 'id');
    }

    public function pop()
    {
        return $this->hasOne(PayOnPortal::class, 'emd_id', 'emd_id');
    }

    public function bt()
    {
        return $this->hasOne(BankTransfer::class, 'emd_id', 'emd_id');
    }

    public function dd()
    {
        return $this->hasOne(EmdDemandDraft::class, 'emd_id', 'emd_id');
    }

    public function bg()
    {
        return $this->hasOne(EmdBg::class, 'emd_id', 'emd_id');
    }

    public function chq()
    {
        return $this->hasOne(EmdCheque::class, 'emd_id', 'emd_id');
    }

    public function fdr()
    {
        return $this->hasOne(EmdFdr::class, 'emd_id', 'emd_id');
    }
}
