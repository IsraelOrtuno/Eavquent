<?php

use Devio\Propertier\Propertier;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \Devio\Eavquent\EntityAttributeValues;

    protected $fillable = ['name'];

    public $timestamps = false;

}