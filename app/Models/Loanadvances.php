<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loanadvances extends Model
{
    //
    
    function loanadvances(){
        return $this->hasOne(Loanpartname::class,'id','loanparty_name');
    }
     public function dueemi()
    {
        return $this->hasOne(Dueemi::class, 'loneid', 'id'); 
    }
    
}
