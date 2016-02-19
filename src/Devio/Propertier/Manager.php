<?php

namespace Devio\Propertier;


class Manager
{
    /**
     * @var array
     */
    protected $deletionQueue = [];

    public function register($name, $type, $multivalue, $entity)
    {

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