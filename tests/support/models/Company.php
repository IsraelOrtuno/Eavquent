<?php

use Devio\Propertier\Propertier;

class Company extends Propertier
{

    protected $morphClass = 'Company';

    protected $fillable = ['name'];

}