<?php namespace Devio\Propertier\Relations;

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
        $results = $this->transformValuesIntoProperty(
            parent::getResults(), $this->getEntityProperties()
        );

        return $results;
    }

    /**
     * Perform the transformation of the values.
     *
     * @param $values
     *
     * @param $property
     *
     * @return mixed
     */
    protected function transformValuesIntoProperty($values, $property)
    {
        $transformer = new Transformer;

        $transformer->properties($this->getEntityProperties())
                    ->values($values)
                    ->transform();

        return $transformer->transformValuesIntoProperty($values, $property);
    }

    /**
     * @return mixed
     */
    protected function getEntityProperties()
    {
        return $this->getParent()
                    ->getRelationValue('properties');
    }
}
