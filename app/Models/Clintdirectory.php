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
        'phone_no',
        'ip',
        'strtotime',
        'courier_addr',
    ];
}
