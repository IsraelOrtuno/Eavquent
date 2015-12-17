<?php

use Devio\Propertier\Propertier;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use Propertier;

    protected $morphClass = 'Employee';

    protected $fillable = ['name'];

}