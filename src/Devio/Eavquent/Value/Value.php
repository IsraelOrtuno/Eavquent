<?php

namespace Devio\Eavquent\Value;

use Illuminate\Database\Eloquent\Model;

abstract class Value extends Model
{
    /**
     * Model timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

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
     * Get the content.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->getAttribute('content');
    }

    /**
     * Get the attribute table name.
     *
     * @return string
     */
    private function getAttributeTableName()
    {
        $class = str_replace('Value', '', class_basename($this));

        return eav_value_table($class);
    }
}