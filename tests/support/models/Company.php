<?php

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    use Devio\Propertier\PropertierTrait;

    protected $morphClass = 'Company';

    public function employees()
    {
        return $this->hasMany('Employee');
    }

}