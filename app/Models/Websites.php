<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Websites extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url'];


    public function tenders()
    {
        return $this->hasMany(TenderInfo::class, 'website', 'id');
    }
}
