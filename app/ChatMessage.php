<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
}
