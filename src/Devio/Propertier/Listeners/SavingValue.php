<?php

namespace Devio\Propertier\Listeners;

use Illuminate\Contracts\Validation\Factory as Validator;
use Illuminate\Validation\ValidationException;

class SavingValue
{
    /**
     * The validator instance.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * SavingValue constructor.
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Handling before saving.
     *
     * @param $model
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    public function handle($model)
    {
        $model->syncRelations();

        // We will validate that the model passes the minimum validation for being
        // stored which are just the needed fields for keeping relation between
        // the value with the property and entity models. Value is not needed.
        $validator = $this->getValidatorInstance($model);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate the value model relations.
     *
     * @param $model
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws ValidationException
     */
    protected function getValidatorInstance($model)
    {
        return $this->validator->make($model->toArray(), $this->rules($model));
    }

    /**
     * Get rules for model.
     *
     * @param $model
     * @return array
     */
    protected function rules($model) {
        return [
            $model->entity()->getMorphType() => 'required',
            $model->entity()->getForeignKey() => 'required',
            $model->property()->getForeignKey() => 'required'
        ];
    }
}