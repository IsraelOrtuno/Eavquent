<?php

namespace Devio\Eavquent\Value;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractValue extends Model
{
    /**
     * Prefix for value tables.
     */
    const TABLE_PREFIX = 'eav_values_';

    /**
     * Attribute constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = $this->getAttributeTableName();

        parent::__construct($attributes);
    }

    /**
     * Get the attribute table name.
     *
     * @return string
     */
    private function getAttributeTableName()
    {
        $class = str_replace('Value', '', get_class());

        return self::TABLE_PREFIX . studly_case($class);
    }
}