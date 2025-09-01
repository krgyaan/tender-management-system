<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceFeedback extends Model
{
    use HasFactory;

    protected $table = 'service_customer_feedback';

    protected $fillable = [
        'complaint_id',
        'problem_resolved',
        'satisfaction',
        'rating',
        'suggestions',
    ];
}