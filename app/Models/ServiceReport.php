<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReport extends Model
{
    use HasFactory;

    // Table name (optional, if it doesn't follow plural convention)
    protected $table = 'service_reports';

    // Primary Key
    protected $primaryKey = 'id';

    // Mass assignable attributes
    protected $fillable = [
        'complaint_id',
        'service_engineer_id',
        'remarks',
        'resolution_done',
        'unsigned_photo',
        'signed_photo',
        'resolved_photo',
    ];

    // Casting for resolution_done to treat ENUM('0','1') as boolean
    protected $casts = [
        'resolution_done' => 'boolean',
    ];

    // Relationships
    public function complaint()
    {
        return $this->belongsTo(CustomerComplaint::class, 'complaint_id');
    }

    public function serviceEngineer()
    {
        return $this->belongsTo(ServiceEngineer::class, 'service_engineer_id');
    }
}