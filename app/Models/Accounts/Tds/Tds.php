<?php

namespace App\Models\Accounts\Tds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Tds extends Model
{
    use HasFactory;

    protected $fillable = [
        'tds_excel_path',
        'tally_data_link',
        'tds_challan_path',
        'tds_payment_challan_path',
        'tds_return_path'
    ];



    public function payments()
    {
        return $this->hasMany(TdsPayment::class);
    }
}