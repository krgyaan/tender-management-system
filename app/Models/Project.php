<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'organisation_id',
        'item_id',
        'location_id',
        'po_no',
        'project_code',
        'project_name',
        'po_upload',
        'po_date'
    ];

    protected $casts = [
        'po_date' => 'date'
    ];

    public function organisation()
    {
        return $this->belongsTo(Organization::class, 'organisation_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
