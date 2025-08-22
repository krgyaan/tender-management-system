<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'name',
        'designation',
        'phone',
        'email',
        'source'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
