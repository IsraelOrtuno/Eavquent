<?php

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \Devio\Eavquent\Eavquent;

    protected $fillable = ['name'];

    public $timestamps = false;
    
    public function __call($method, $parameters)
    {
        if($eav_call = $this->eavquentMagicMethodCall($method,$parameters)) return $eav_call;
       
        //if($other_package_call = $this->otherPackageMagicMethodCall($method,$parameters)) return $other_package_call;
       
        return parent::__call($method, $parameters);
    }

}
