<?php
namespace Devio\Propertier\Validators;

use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;

abstract class AbstractValidator
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Attributes under validation.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Creates a new validator instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Making an object with attributes to validate in a static way.
     *
     * @param array $attributes
     *
     * @return static
     */
    public static function make(array $attributes = [])
    {
        return new static($attributes);
    }

    /**
     * Returns the validation attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * The validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Performs the validation.
     *
     * @throws ValidationException
     */
    public function validate()
    {
        $instance = $this->getValidatorInstance();

        if ( ! $instance->passes())
        {
            $this->failedValidation($instance);
        }
    }

    /**
     * Provides the validator instance.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $factory = $this->container->make('Illuminate\Validation\Factory');

        return $factory->make(
            $this->getAttributes(), $this->rules()
        );
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @return mixed
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    /**
     * Get the container.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the container
     *
     * @param Container $container
     *
     * @return AbstractValidator
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return AbstractValidator
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }
}