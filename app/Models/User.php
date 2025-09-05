<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'mobile',
        'address',
        'id_proof',
        'designation',
        'image',
        'permissions',
        'status',
        'team',
        'app_password',
        'sign'
    ];

    public function oauth()
    {
        return $this->hasOne(Tbl_google_access_token::class, 'userid', 'id');
    }
}
