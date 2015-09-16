<?php
namespace Devio\Propertier;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Devio\Propertier\Exceptions\ValuesRelationAlreadyLoaded;

class ValueLinker
{
    /**
     * @var Collection
     */
    protected $values;

    /**
     * @var Collection
     */
    protected $properties;

    /**
     * @return mixed
     */
    public function link()
    {
        return $this->properties instanceof Collection
            ? $this->linkMany()
            : $this->linkOne($this->properties);
    }

    /**
     * @param $property
     *
     * @return mixed
     * @throws ValuesRelationAlreadyLoaded
     */
    protected function linkOne($property)
    {
        if ($property->relationLoaded('values'))
        {
            throw new ValuesRelationAlreadyLoaded;
        }

        // If the property already contains a values relationship, we do not
        // want to interfiere, this will be a breaking error. If not will
        // initialize the relation with the values that belong to it.
        $property->setRelation('values', $this->valuesOf($property));

        return $property;
    }

    /**
     * @return mixed
     */
    protected function linkMany()
    {
        foreach ($this->properties as $property)
        {
            $this->linkOne($property);
        }

        return $this->properties;
    }

    /**
     * Get the values based on a property given. A model instance or property
     * id are accepted.
     *
     * @param $property
     *
     * @return Collection
     */
    public function valuesOf($property)
    {
        if ( ! $property instanceof Model)
        {
            // If the value passed is not a model instance, it will be assumed
            // as a property ID. We will just create a dummy property model
            // which will only contain that key value for the condition.
            $abstract = new Property;
            $abstract->setAttribute($abstract->getKeyName(), $property);
            $property = $abstract;
        }

        // Will filter through the values collection looking for those values that
        // are matching the property passed as parameter. The where method gets
        // the current property ID and return the values of same property_id.
        return $this->values->where(
            $property->getForeignKey(), $property->getKey()
        );
    }

    /**
     * @param $property
     *
     * @return $this
     */
    public function properties($property)
    {
        $this->properties = $property;

        return $this;
    }

    /**
     * @param $values
     *
     * @return $this
     */
    public function values($values)
    {
        $this->values = $values;

        return $this;
    }
    
}