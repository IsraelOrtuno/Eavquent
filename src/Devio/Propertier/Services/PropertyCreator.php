<?php
namespace Devio\Propertier\Services;

use Devio\Propertier\Validators\CreateProperty;

class PropertyCreator
{
    /**
     * @var CreateProperty
     */
    protected $validator;

    /**
     * @param CreateProperty $validator
     */
    public function __construct(CreateProperty $validator)
    {
        $this->validator = $validator;
    }

    public function create(array $attributes)
    {

    }

}