<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationReference extends Model
{
    protected $fillable = ['conversation_map_id', 'rfc_message_id'];
    public $timestamps = true;

    public function map()
    {
        return $this->belongsTo(ConversationMap::class, 'conversation_map_id');
    }
}
