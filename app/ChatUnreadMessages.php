<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatUnreadMessages extends Model
{
   	protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

}
