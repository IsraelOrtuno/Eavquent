<?php
namespace Devio\Propertier;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Services\ValueGetter;
use Devio\Propertier\Services\ValueSetter;
use Illuminate\Database\Eloquent\Collection;
use Devio\Propertier\Relations\HasManyProperties;
use Devio\Propertier\Observers\PropertierObserver;

abstract class Propertier extends Model
{

}