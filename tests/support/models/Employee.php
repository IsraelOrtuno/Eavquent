<?php

use Devio\Propertier\Propertier;

class Employee extends Propertier
{

    protected $morphClass = 'Employee';

    protected $fillable = ['name'];

}