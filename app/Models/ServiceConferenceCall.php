<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ServiceConferenceCall extends Model
{
    use HasFactory;
  protected $table = 'service_conference_call_reports';  

    protected $fillable = [
        'complaint_id',
        'issue_description',
        'materials_required',
        'actions_planned',
        'voice_recording_path',
        'attachments',
        'created_by',
    ];

    // Cast attachments JSON to array automatically
    protected $casts = [
        'attachments' => 'array',
    ];

    // Relationships
    public function complaint()
    {
        return $this->belongsTo(CustomerComplaint::class);
    }

    // Accessor for voice recording URL
    public function getVoiceRecordingUrlAttribute()
    {
        return $this->voice_recording_path
            ? \Storage::disk('public')->url($this->voice_recording_path)
            : null;
    }

    // Accessor for attachment URLs (array)
    public function getAttachmentsUrlsAttribute()
    {
        if (!$this->attachments) return [];
        return array_map(function ($path) {
            return \Storage::disk('public')->url($path);
        }, $this->attachments);
    }
}