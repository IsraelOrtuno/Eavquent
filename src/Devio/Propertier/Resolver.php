<?php

namespace Devio\Propertier;

use RuntimeException;

class Resolver
{
    /**
     * All of the value types.
     *
     * @var array
     */
    protected static $valueTypes = [];

    /**
     * Register the fields.
     *
     * @param $valueTypes
     */
    public static function register($valueTypes)
    {
        static::$valueTypes = $valueTypes;
    }

    /**
     * Guess the value type based on field.
     *
     * @param Field $field
     * @return string
     */
    public function field($field)
    {
        return $this->getClassName($field);
    }

    /**
     * Resolves the field classpath.
     *
     * @param $field
     * @return PropertyAbstract
     */
    public function getClassName($field)
    {
        $type = $this->getFieldType($field);

        if (is_null($type) || ! isset(static::$valueTypes[$type])) {
            throw new RuntimeException('Error when resolving unregisterd value type type');
        }

        return static::$valueTypes[$type];
    }

    /**
     * Get the type of a field.
     *
     * @param $field
     * @return mixed
     */
    protected function getFieldType($field)
    {
        if (is_string($field)) {
            return $field;
        }

        return $field instanceof Field ?
            $field->getAttribute('type') : null;
    }
}
