<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employeeimprestamount extends Model
{
    public function project()
    {
        return $this->hasOne(Employeeimprest::class, 'id', 'project_name');
    }

    public function empImp()
    {
        return $this->hasOne(Employeeimprest::class, 'id', 'name_id');
    }
}
