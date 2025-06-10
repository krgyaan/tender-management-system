<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUpPersons extends Model
{
    use HasFactory;

    protected $fillable = [
        'follwup_id',
        'org',
        'name',
        'email',
        'phone',
    ];

    public function followup()
    {
        return $this->belongsTo(FollowUps::class, 'follwup_id');
    }
}
