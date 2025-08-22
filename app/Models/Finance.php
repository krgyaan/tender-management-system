<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'document_name',
        'document_type',
        'financial_year',
        'image',
        'status',
        'ip',
        'strtotime',
    ];
    public function financialyear()
    {
        return $this->hasOne(Financialyear::class, 'id', 'financial_year');
    }
    public function documenttype()
    {
        return $this->hasOne(Documenttype::class, 'id', 'document_type');
    }
}
