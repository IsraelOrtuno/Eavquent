<?php namespace Devio\Propertier\Properties;

use Devio\Propertier\Models\Property;
use Devio\Propertier\Models\PropertyValue;

abstract class PropertyAbstract {

    /**
     * The property model.
     *
     * @var Property
     */
    protected $property;

    /**
     * The property value model.
     *
     * @var PropertyValue
     */
    protected $value;

    /**
     * The PropertyValue value. This property is just for allowing quick access.
     *
     * @var string
     */
    protected $plainValue;

    /**
     * Creates a new Property.
     *
     * @param Property $property
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * Decorates the property value before it is returned. This will
     * be useful as value formatter.
     *
     * @return mixed
     */
    public function decorate()
    {
        return $this->plainValue;
    }

    /**
     * Will set the PropertyValue assigned to this property.
     *
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        $value->value = $this->cast($value->value);

        $this->value = $value;

        $this->plainValue = $value->value;

        return $this;
    }

    /**
     * Will cast the PropertyValue plain value in case it is going to
     * be used as the property type.
     *
     * @param $plainValue
     */
    protected function cast($plainValue)
    {
        return $plainValue;
    }

    /**
     * Check if the value is valid to be persisted.
     *
     * @return bool
     */
    public function isValidForStorage()
    {
        return true;
    }

    /**
     * Will return the property value object.
     *
     * @return PropertyValue
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Will return the plain value already casted.
     *
     * @return string
     */
    public function getPlainValue()
    {
        return $this->plainValue;
    }
}