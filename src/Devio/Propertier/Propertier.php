<?php
namespace Devio\Propertier;

use Devio\Propertier\Models\Property;
use Devio\Propertier\Validators\RegisterProperty;
use Illuminate\Contracts\Events\Dispatcher as Event;

class Propertier
{
    /**
     * The event dispatcher
     *
     * @var Event
     */
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @param array $attributes
     * @param null $callback
     * @return Property
     */
    public function register(array $attributes, $callback = null)
    {
        $this->event->fire('propertier.registering', $attributes);

        RegisterProperty::make($attributes)->validate();

        $property = new Property($attributes);

        $this->event->fire('propertier.registered', $property);

        return $property;
    }

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}