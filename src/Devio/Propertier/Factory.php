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
     * The manager instance.
     *
     * @var Manager
     */
    protected $manager;

    /**
     * Handler constructor.
     *
     * @param $partner
     * @param null $manager
     */
    public function __construct($partner, $manager = null)
    {
        $this->partner = $partner;
        $this->manager = $manager ?: new Manager;

        $this->bootPartnerRelations();
    }

    /**
     * Booting the partner relations.
     */
    protected function bootPartnerRelations()
    {
        $partner = $this->getPartner();

        // We will spin through any partner field and register if it were a real
        // relationship into the partner model. We will dynamically register a
        // closure which will return a relation object based on the field type.
        foreach ($this->getFields() as $field) {
            $partner->setFieldRelation(
                $field->getName(), $this->getRelationClosure($field)
            );
        }
    }

    /**
     * Get the field instance for a value.
     *
     * @param $value
     * @return mixed
     */
    public static function getValueField($value)
    {
        $fields = with($manager = new Manager)->getFields();

        return $fields->where('id', $value->field_id)->first();
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
        if (is_null($value)) {
            $value = [];
        } elseif ($value instanceof BaseCollection) {
            $value = $value->all();
        } elseif (! is_array($value)) {
            $value = [$value];
        }

        // We only want to store Eloquent Collections so we will transform any
        // value input to get a proper formed Eloquent Collection. When done
        // we just have to set it as relationship for the partner instance.
        return $this->assign($field, new Collection($value));
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
        // Here is where we assign any value as relationship of any field name.
        // We will establish the raw value instance either is a plain object
        // or a collection to the relation which is matching the field key.
        return $this->getPartner()->setRelation($field->getName(), $value);
    }

    /**
     * Get the value of a field.
     *
     * @param $key
     * @param $attribute
     * @return mixed
     */
    public function get($key, $attribute)
    {
        // Just in case the user has set any get mutator into the main model
        // that corresponds to a field, we will assume that the user will
        // provide its own output and we will not modify anything else.
        if (is_null($attribute) || $this->getPartner()->hasGetMutator($key)) {
            return $attribute;
        }

        $field = $this->getField($key);

        // If the value is multivalued, we will provide a collection plucked by
        // pluck by the value and its id. If is not we'll just grab the value
        // of the item. Both cases have ran any mutator on their type class.
        if ($field->isMultivalue()) {
            return $attribute->pluck('value', 'id');
        }

        return $attribute->getAttribute('value');
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
        return Closure::bind(function () use ($relation, $field) {
            return $relation->where($field->getForeignKey(), $field->getKey());
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
     * Check if key matches any partner relation, PK or model column.
     *
     * @param $key
     * @return bool
     */
    public function findConflicts($key)
    {
        return $this->isRelationship($key)
        || $this->isModelColumn($key)
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
     * Get the fields registered for the current partner.
     *
     * @return mixed
     */
    protected function getFields()
    {
        return $this->manager->getFields($this->getPartnerClass());
    }

    /**
     * Get a field by name.
     *
     * @param $key
     * @return mixed
     */
    public function getField($key)
    {
        return $this->getFields()->get($key);
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
     * Get the base model attribute names.
     *
     * @return array
     */
    public function getModelColumns()
    {
        $class = get_class($this->getPartner());

        // We have to resolve the partner class name in order to access its static
        // property $modelColums. This property is stored in the model as will
        // be different from one model to another.
        if (empty($class::$modelColumns)) {
            $class::$modelColumns = $this->fetchModelColumns();
        }

        // If no attributes are listed into $modelColumns property, we will
        // fetch them from database. This could result into a performance
        // issue so it should be set manually or when booting the model.
        return $class::$modelColumns;
    }

    /**
     * Check if an attribute corresponds to a model column name.
     *
     * @param $attribute
     * @return bool
     */
    public function isModelColumn($attribute)
    {
        return in_array($attribute, $this->getModelColumns());
    }

    /**
     * Get the model column names.
     *
     * @return mixed
     */
    public function fetchModelColumns()
    {
        return with($partner = $this->getPartner())
            ->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($partner->getTable());
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
    protected function getPartner()
    {
        return $this->partner;
    }
}