<?php namespace Devio\Propertier\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyValues extends HasMany
{
    use TransformDictionary;
}