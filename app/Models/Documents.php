<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;

    protected $fillable = [
        'phydoc_id',
        'name',
        'file',
    ];

    public function phydocs()
    {
        return $this->belongsTo(PhyDocs::class, 'phydoc_id', 'id');
    }
}
