<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_google_access_token extends Model
{
    protected $fillable = ['userid', 'access_token', 'refresh_token', 'expires_in', 'token_type', 'scope', 'ip'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }
}
