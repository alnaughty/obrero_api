<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
	protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'user_id', 'user_id_x'];

    public function messages()
    {
    	return $this->hasMany(ChatMessage::class, 'chat_id');
    }
    public function unread()
    {
    	return $this->hasOne(ChatUnreadMessages::class);
    }
}
