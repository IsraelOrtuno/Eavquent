<?php namespace Devio\Propertier\Services;

use Devio\Propertier\Models\Property;
use Devio\Propertier\Validators\CreateProperty;
use Illuminate\Contracts\Events\Dispatcher as Event;

class PropertyCreator
{
    /**
     * @var CreateProperty
     */
    protected $validator;

    /**
     * @param CreateProperty $validator
     */
    public function __construct(CreateProperty $validator)
    {
        $this->validator = $validator;
    }

    public function create(array $attributes)
    {

    }

}