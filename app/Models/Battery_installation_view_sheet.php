<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battery_installation_view_sheet extends Model
{
    //
    
    function batteryinstall(){
        return $this->hasOne(Battery_installation::class,'id','batteryinstallation_id');
    }
}
