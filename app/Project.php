<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    public function owner()
    {
    	return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function warnings()
    {
    	return $this->hasMany(ProjectWarning::class,'project_id');
    }
}
