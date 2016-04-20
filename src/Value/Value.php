<?php

namespace Devio\Eavquent\Value;

use Devio\Eavquent\Attribute\Attribute;
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
     * Relationship to the attributes table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Set the content.
     *
     * @param $content
     * @return mixed
     */
    public function setContent($content)
    {
        return $this->setAttribute('content', $content);
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
     * Check if value should push to relations when saving.
     *
     * @return bool
     */
    public function shouldPush()
    {
        return false;
    }

    /**
     * Get the attribute table name.
     *
     * @return string
     */
    protected function getAttributeTableName()
    {
        $class = str_replace('Value', '', class_basename($this));

        return eav_value_table($class);
    }

    /**
     * Return an Eavquent Collection instead.
     *
     * @param  array $models
     * @return Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }
}
