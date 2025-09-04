<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceEngineer extends Model
{
    use HasFactory;

    protected $table = 'service_engineers';

    protected $fillable = [
        'complaint_id',
        'name',
        'phone',
        'email',
        'status',
    ];


    public function getComplaint()
    {
        return $this->belongsTo(ServiceComplaint::class, 'complaint_id');
    }
}