<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadMailFollowup extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id',
        'user_id',
        'mail_body',
        'attachment_path',
        'frequency',
        'stop_reason',
        'proof_text',
        'proof_image',
        'remarks'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
