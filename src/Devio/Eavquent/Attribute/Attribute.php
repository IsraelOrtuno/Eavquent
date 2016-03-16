<?php

namespace Devio\Eavquent\Attribute;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * Model timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'label', 'model', 'entity', 'default_value', 'collection'
    ];

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
     * Check if attribute is multivalued.
     *
     * @return bool
     */
    public function isCollection()
    {
        return (bool) $this->getAttribute('collection');
    }
}