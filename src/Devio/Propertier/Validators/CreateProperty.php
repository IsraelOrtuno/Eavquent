<?php namespace Devio\Propertier\Validators;

class CreateProperty extends AbstractValidator
{
    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'type'       => 'required',
            'name'       => 'required|min:2',
            'multivalue' => 'boolean',
            'entity'     => 'required'
        ];
    }

}