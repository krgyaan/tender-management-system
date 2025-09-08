<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationMap extends Model
{
    protected $fillable = ['user_id', 'conversation_key', 'thread_id', 'last_message_id', 'meta'];
    protected $casts = ['meta' => 'array'];
}
