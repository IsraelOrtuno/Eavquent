<?php

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function company()
    {
        return $this->belongsTo('Company');
    }
}