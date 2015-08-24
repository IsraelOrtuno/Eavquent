<?php

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    use Devio\Propertier\PropertierTrait;

    protected $morphClass = 'Company';
    
}