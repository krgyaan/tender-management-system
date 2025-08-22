<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Battery_item_price extends Model
{
    //
    
   public function itemModel()
{
    return $this->hasOne(Item_model::class, 'id', 'item_model');
}

    
    
      public static function houseajexbatteryprice($draw,$searchValue,$columnSortOrder,$columnName,$columnIndex,$filltersearchValue,$rowcount,$length){
    
     $start = $draw;
        $query = DB::table('battery_item_prices')->orderby('id','DESC');
     
        if($searchValue){
        $query->where(function ($q) use ($searchValue){
        $q->where('item_name', 'like', '%' . $searchValue . '%');
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
