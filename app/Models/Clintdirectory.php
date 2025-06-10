<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clintdirectory extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization',
        'name',
        'designation',
        'phone_no',
        'email',
        'ip',
        'strtotime',
        'courier_addr',
    ];
}
