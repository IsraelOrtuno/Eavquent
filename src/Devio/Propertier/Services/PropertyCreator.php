<?php namespace Devio\Propertier\Services;

use Devio\Propertier\Models\Property;
use Devio\Propertier\Validators\CreateProperty;
use Illuminate\Contracts\Events\Dispatcher as Event;

class PropertyCreator
{

    protected $event;

    /**
     * @var CreateProperty
     */
    protected $validator;

    /**
     * @param Event $event
     * @param CreateProperty $validator
     */
    public function __construct(Event $event, CreateProperty $validator)
    {
        $this->event = $event;
        $this->validator = $validator;
    }

    public function create(array $attributes, $callback = null)
    {
        $this->event->fire('propertier.registering', $attributes);

        $this->validator->validate($attributes);

        $property = new Property($attributes);

        $this->event->fire('propertier.registered', $property);

        return $property;
    }

}