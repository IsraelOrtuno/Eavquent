<?php
namespace Devio\Propertier\Relations;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyValues extends MorphMany
{
    use TransformDictionary;
}