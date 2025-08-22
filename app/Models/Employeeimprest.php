<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employeeimprest extends Model
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'name_id');
    }
    
    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    
    public function team()
    {
        return $this->hasOne(User::class, 'id', 'team_id');
    }
    
    public function project()
    {
        return $this->hasOne(Employeeimprest::class, 'name_id', 'project_name');
    }
    
    public static function getAmtReceived($id = null)
    {
        try {
            if ($id) {
                $amtReceived = Employeeimprestamount::where('name_id', $id)->sum('amount');
            } else {
                $amtReceived = Employeeimprestamount::sum('amount');
            }
            return $amtReceived;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching amount received']);
        }
    }

    public static function getAmtSpent($id = null)
    {
        try {
            if ($id) {
                $amtSpent = EmployeeImprest::where('name_id', $id)->sum('amount');
            } else {
                $amtSpent = EmployeeImprest::sum('amount');
            }
            return $amtSpent;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching amount spent']);
        }
    }

    public static function getAmtApproved($id = null)
    {
        try {
            if ($id) {
                $amtApproved = EmployeeImprest::where('name_id', $id)->where('buttonstatus', '1')->sum('amount');
            } else {
                $amtApproved = EmployeeImprest::where('buttonstatus', '1')->sum('amount');
            }
            return $amtApproved;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching amount approved']);
        }
    }

    public static function getAmtLeft($id = null)
    {
        try {
            $received = self::getAmtReceived($id);
            $spent = self::getAmtApproved($id);
            return $spent - $received;
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Somethig went wrong']);
        }
    }
}
