<?php
namespace Devio\Propertier\Validators;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Factory as ValidatorFactory;

abstract class AbstractValidator
{
    /**
     * Attributes under validation.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Generates a new validator with or without attributes.
     *
     * @param array|null $attributes
     * @return static
     */
    public static function make(array $attributes = [])
    {
        $instance = new static;

        return empty($attributes)
            ? $instance
            : $instance->attributes($attributes);
    }

    /**
     * Will set the attributes to validate.
     *
     * @param array $attributes
     * @return $this
     */
    public function attributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
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
        $validator = ValidatorFactory::make(
            $this->getAttributes(), $this->rules()
        );

        return $validator;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return mixed
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }
}