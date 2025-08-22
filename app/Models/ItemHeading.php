<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemHeading extends Model
{
    protected $fillable = [
        'name',
        'team',
        'status'
    ];
    
    public function items()
    {
        return $this->hasMany(Item::class, 'heading', 'name')
                    ->whereColumn('items.team', 'item_headings.team');
    }

}
