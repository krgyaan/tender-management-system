<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhydocsPerson extends Model
{
    use HasFactory;

    protected $table = 'phydocs_people';
    protected $fillable = [
        'phydoc_id',
        'name',
        'email',
        'phone',
    ];

    public function phydoc()
    {
        return $this->belongsTo(PhyDocs::class);
    }
}
