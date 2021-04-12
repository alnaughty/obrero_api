<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at'];

    public function status()
    {
    	return $this->hasOne(CustomerPayment::class, 'customer_id');
    }

    public function projects()
    {
    	return $this->hasMany(Project::class, 'customer_id');
    }
}
