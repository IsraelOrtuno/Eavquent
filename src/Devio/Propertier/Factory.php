<?php

namespace Devio\Propertier;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection as BaseCollection;

class Factory
{
    /**
     * The partner instance.
     *
     * @var Model
     */
    protected $partner;

    /**
     * Handler constructor.
     *
     * @param $partner
     */
    public function __construct($partner)
    {
        $this->partner = $partner;
    }

    /**
     * Create the partner relations from plain array of values.
     *
     * @param BaseCollection $values
     */
    public function allocate(BaseCollection $values)
    {
        $fields = $this->getFields();

        $values = $values->groupBy($this->getFieldForeignKey());

        // By grouping values by field id will let us find and access them very
        // easy. Just iterate to every property linked to this entity and set
        // the values of any every of them as a regular Eloquent relation.
        foreach ($fields as $field) {
            $this->set($field->getName(), $values->get($field->getKey()));
        }
    }

    /**
     * Set the field value.
     *
     * @param $key
     * @param $value
     * @return Factory
     */
    public function set($key, $value)
    {
        // We do not want to interfiere into the Eloquent default funcionality of
        // the partner instance so we'll check for any coincidence between the
        // field name we are setting and the current partner configuration.
        if ($this->findConflicts($key)) {
            throw new \InvalidArgumentException("{$key} is part of the base Model and could not be set / loaded.");
        }

        // Once we are sure we are not affecting Eloquent workflow, we'll just
        // set a single (plain Value object) or multiple (Collection) to the
        // field depending on wether it has been set as multivalued field.
        $field = $this->getField($key);

        if (! $field->isMultivalue()) {
            return $this->setOne($field, $value);
        }

        return $this->setMany($field, $value);
    }

    /**
     * Set a simple value for a field.
     *
     * @param $field
     * @param $value
     * @return $this
     */
    protected function setOne($field, $value)
    {
        if (is_array($value) || $value instanceof BaseCollection) {
            $value = $value[0];
        }

        return $this->assign($field, $value);
    }

    /**
     * Set value as a collection for multivalued fields.
     *
     * @param $field
     * @param $value
     * @return $this
     */
    protected function setMany($field, $value)
    {
        if ($value instanceof BaseCollection) {
            $value = new Collection($value->all());
        } elseif (! is_array($value)) {
            $value = new Collection([$value]);
        }

        // We only want to store Eloquent Collections so we will transform any
        // value input to get a proper formed Eloquent Collection. When done
        // we just have to set it as relationship for the partner instance.
        return $this->assign($field, $value);
    }

    /**
     * Set the value of a partner relationship.
     *
     * @param $field
     * @param $value
     * @return $this
     */
    protected function assign($field, $value)
    {
        $closure = $this->getRelationClosure($field);

        // Here is where we assign any value as relationship of any field name.
        // We will set the value of the relation to the given value and also
        // dynamically register the field name as if it's a real relation.
        with($partner = $this->getPartner())
            ->setRelation($field->getName(), $value);

        return $partner->setFieldRelation($field, $closure);
    }

    /**
     * Generate the relation closure.
     *
     * @param $field
     * @return Closure
     */
    protected function getRelationClosure($field)
    {
        $partner = $this->getPartner();
        $relation = $this->getBaseRelation($field);

        // This will return a closure fully binded to the partner model instance.
        // This will help us to simulate any relation as if it was handly made
        // in the original partner definition using the function statement.
        return Closure::bind(function () use ($relation) {
            return $relation;
        }, $partner, get_class($partner));
    }

    /**
     * Get the base relation to use.
     *
     * @param $field
     * @return mixed
     */
    protected function getBaseRelation($field)
    {
        $relation = $this->getBaseRelationMethod($field);

        return $this->getPartner()->$relation(Value::class, 'partner');
    }

    /**
     * Get the relation name to use.
     *
     * @param $field
     * @return string
     */
    protected function getBaseRelationMethod($field)
    {
        return $field->isMultivalue() ? 'morphMany' : 'morphOne';
    }

    /**
     * Get a field by name.
     *
     * @param $key
     * @return mixed
     */
    public function getField($key)
    {
        return $this->getFields()->where('name', $key)->first();
    }

    /**
     * Check if key matches any partner relation, PK or model column.
     *
     * @param $key
     * @return bool
     */
    public function findConflicts($key)
    {
        // TODO: Performance could be improved. Consider running this only once as it's currently
        // TODO: being called every time a value is set and when loading raw it may not be needed.
        return $this->isRelationship($key)
//        || $this->isModelColumn($key)
        || $this->isPrimaryKey($key);
    }

    /**
     * Check if the key is an existing partner relationship.
     *
     * @param $key
     * @return bool
     */
    public function isRelationship($key)
    {
        // TODO: Performance may be improved here by storing partner class methods
        if (method_exists($partner = $this->getPartner(), $key)) {
            return $partner->$key() instanceof Relation;
        }

        return $partner->relationLoaded($key);
    }

    /**
     * Check if the key corresponds to the partner PK.
     *
     * @param $key
     * @return bool
     */
    public function isPrimaryKey($key)
    {
        return $this->getPartner()->getKeyName() == $key;
    }

    /**
     * Get the field foreign key name.
     *
     * @return string
     */
    protected function getFieldForeignKey()
    {
        return (new Field)->getForeignKey();
    }

    /**
     * Get the fields registered for the current partner.
     *
     * @return mixed
     */
    protected function getFields()
    {
        // TODO: Cache this query
        return Field::where('partner', $this->getPartnerClass())->get();
    }

    /**
     * Get the partner class name.
     *
     * @return string
     */
    protected function getPartnerClass()
    {
        return $this->getPartner()->getMorphClass();
    }

    /**
     * Get the partner instance.
     *
     * @return Model
     */
    public function getPartner()
    {
        return $this->partner;
    }

}