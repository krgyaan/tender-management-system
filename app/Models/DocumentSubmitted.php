<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSubmitted extends Model
{
    use HasFactory;

    protected $table = 'document_submitteds';

    protected $fillable = [
        'name',
    ];
}
