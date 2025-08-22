<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'company_name',
        'name',
        'designation',
        'phone',
        'email',
        'address',
        'country',
        'state',
        'type',
        'industry',
        'team',
        'bd_person',
        'points_discussed',
        've_responsibility',
        'mail_followup_count',
        'call_followup_count',
        'visit_followup_count',
        'letter_sent_count',
        'whatsapp_followup_count',
        'enquiry_received_at',
        'last_mail_sent_at',
        'last_call_at',
        'last_visit_at',
        'last_whatsapp_sent_at',
        'last_letter_sent_at'
    ];

    public function mailFollowups()
    {
        return $this->hasMany(LeadMailFollowup::class);
    }

    public function callFollowups()
    {
        return $this->hasMany(LeadCallFollowup::class);
    }

    public function visitFollowups()
    {
        return $this->hasMany(LeadVisitFollowup::class);
    }

    public function letterFollowups()
    {
        return $this->hasMany(LeadLetterFollowup::class);
    }

    public function whatsappFollowups()
    {
        return $this->hasMany(LeadWhatsappFollowup::class);
    }

    public function contacts()
    {
        return $this->hasMany(LeadContact::class);
    }

    public function bd_lead()
    {
        return $this->belongsTo(User::class, 'bd_person');
    }

    // Helper methods to get formatted status
    public function getFollowupStatusAttribute()
    {
        return [
            'mail' => [
                'count' => $this->mail_followup_count,
                'last' => $this->last_mail_sent_at?->format('d M Y'),
                'next' => $this->next_followup_date?->format('d M Y'),
            ],
            'call' => [
                'count' => $this->call_followup_count,
                'last' => $this->last_call_at?->format('d M Y'),
            ],
            'visit' => [
                'count' => $this->visit_followup_count,
                'last' => $this->last_visit_at?->format('d M Y'),
            ],
            'whatsapp' => [
                'count' => $this->whatsapp_followup_count,
                'last' => $this->last_whatsapp_sent_at?->format('d M Y'),
            ],
            'enquiry_received' => $this->enquiry_received_at?->format('d M Y'),
        ];
    }
}
