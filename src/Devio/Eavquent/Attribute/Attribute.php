<?php

namespace Devio\Eavquent\Attribute;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    // Attribute code column
    const COLUMN_CODE = 'code';
    // Attribute type column
    const COLUMN_MODEL = 'model';
    // Entity the attribute belongs to.
    const COLUMN_ENTITY = 'entity';
    // Attribute default value column.
    const COLUMN_DEFAULT_VALUE = 'default_value';

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
        $this->setTable(eav_table('attributes'));

        parent::__construct($attributes);
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->getAttribute(static::COLUMN_CODE);
    }

    /**
     * @return mixed
     */
    public function getModelClass()
    {
        return $this->getAttribute(static::COLUMN_MODEL);
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return false;
    }
}