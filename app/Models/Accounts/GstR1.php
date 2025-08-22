<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GstR1 extends Model
{
 use HasFactory;

    protected $table = 'gstr1';

    protected $fillable = [
        'gst_r1_sheet_path',
        'tally_data_link',
        'confirmation',
        'return_file_path',
        'filed_date'
    ];

    protected $casts = [
        'confirmation' => 'boolean',
        'filed_date' => 'datetime',
    ];

    /**
     * Get the path for the GST R1 sheet file.
     */
    public function getGstR1SheetPathAttribute($value)
    {
        return asset('storage/' . $value);
    }

    /**
     * Get the path for the return file if it exists.
     */
    public function getReturnFilePathAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}
