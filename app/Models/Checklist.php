<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Checklist extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_name',
        'frequency',
        'responsibility',
        'accountability',
        'description',
        'frequency_condition',
    ];

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsibility');
    }

    public function accountableUser()
    {
        return $this->belongsTo(User::class, 'accountability');
    }

    // Helper: Get selected weekday for weekly
    public function getWeeklyDay()
    {
        return $this->frequency === 'Weekly' ? (int) $this->frequency_condition : null;
    }

    // Helper: Get day of month for monthly
    public function getMonthlyDay()
    {
        return $this->frequency === 'Monthly' ? (int) $this->frequency_condition : null;
    }

    public static function testConnection()
    {
        try {
            return (bool) self::query()->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function reports()
    {
        return $this->hasMany(AccountChecklistReport::class);
    }
}
