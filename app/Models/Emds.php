<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emds extends Model
{
    use HasFactory;
    protected $fillable = [
        'tender_id',
        'instrument_type',
        'project_name',
        'requested_by',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderInfo::class, 'tender_id');
    }

    public function emdDemandDrafts()
    {
        return $this->hasMany(EmdDemandDraft::class, 'emd_id', 'id');
    }

    public function emdCheques()
    {
        return $this->hasMany(EmdCheque::class, 'emd_id', 'id');
    }

    public function emdFdrs()
    {
        return $this->hasMany(EmdFdr::class, 'emd_id', 'id');
    }

    public function emdBgs()
    {
        return $this->hasMany(EmdBg::class, 'emd_id', 'id');
    }

    public function emdBankTransfers()
    {
        return $this->hasMany(BankTransfer::class, 'emd_id', 'id');
    }
    public function emdPayOnPortals()
    {
        return $this->hasMany(PayOnPortal::class, 'emd_id', 'id');
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by', 'name');
    }
}
