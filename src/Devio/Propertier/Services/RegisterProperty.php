<?php namespace Devio\Propertier\Services;

class RegisterProperty
{

    protected $event;

    /**
     * @param Event $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function register(array $attributes, $callback = null)
    {
        $validator = $this->getRegisterValidatorInstance();

        $validator->validate();

        $this->event->fire('propertier.registering', $attributes);

        $property = new Property($attributes);

        $this->event->fire('propertier.registered', $property);

        return $property;
    }

}