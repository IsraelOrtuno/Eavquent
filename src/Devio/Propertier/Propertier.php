<?php
namespace Devio\Propertier;

use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Relations\HasManyProperties;

abstract class Propertier extends Model
{
    public function properties()
    {
        $instance = new Property;

        return (new HasManyProperties(
            $instance->newQuery(), $this, $this->getMorphClass()
        ));
    }
}