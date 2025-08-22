<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmdResponsiblity extends Model
{
    protected $fillable = [
        'user_id',
        'responsible_for',
    ];

    public function responsible()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
