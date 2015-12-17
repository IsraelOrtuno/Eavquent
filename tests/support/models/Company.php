<?php

use Devio\Propertier\Propertier;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use Propertier;

    protected $morphClass = 'Company';

    protected $fillable = ['name'];

}