<?php

namespace Devio\Propertier\Relations;

use Devio\Propertier\Transformer;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyValues extends MorphMany
{
    /**
     * Will provide the transformed relation results.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->transformValues(parent::getResults());
    }

    /**
     * Perform the transformation of the values.
     *
     * @param $values
     * @return mixed
     */
    protected function transformValues($values)
    {
        $transformer = new Transformer;

        return $transformer->properties($this->getEntityProperties())
            ->values($values)
            ->transform();
    }

    /**
     * @return mixed
     */
    protected function getEntityProperties()
    {
        return $this->getParent()
            ->getPropertiesRelationValue();
    }
}
