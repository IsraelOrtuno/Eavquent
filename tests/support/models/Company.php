<?php

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \Devio\Eavquent\Eavquent;

    protected $fillable = ['name'];

    public $timestamps = false;

}