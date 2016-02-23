<?php

namespace Devio\Propertier;

use Illuminate\Container\Container;

class Manager
{
    /**
     * @var array
     */
    protected $deletionQueue = [];

    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Manager constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container ?: Container::getInstance();
    }

    /**
     * Get the registered fields.
     *
     * @param null $partner
     * @return mixed
     */
    public function getFields($partner = null)
    {
        $fields = $this->container->make('propertier.fields');

        return $partner ? $fields->get($partner) : $fields->flatten();
    }

    /**
     * Removes null values from database.
     *
     * @return mixed
     */
    public function flush()
    {
        return Value::flush();
    }
}