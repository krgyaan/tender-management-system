<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Battery_installation extends Model
{
    //
    
    
     public static function houseajexbatteryinstallation($draw,$searchValue,$columnSortOrder,$columnName,$columnIndex,$filltersearchValue,$rowcount,$length){
    
    
        $start = $draw;
      
        $query = DB::table('battery_installations')->orderby('id','DESC');
       
        if($searchValue){
        $query->where(function ($q) use ($searchValue){
        $q->where('title', 'like', '%' . $searchValue . '%');
        // $q->where('name', 'like', '%' . $searchValue . '%');
        });
        };
        
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        
        foreach ($bindings as $binding) {
        $sql = preg_replace('/\?/', "'$binding'", $sql, 1);
        }
        
        if($rowcount == 1){
        // $query->limit(10)->offset($start);
        $query->offset($start)->take($length);
        return $query->get();
        }else{
        return $query->count();    
        }}
}
