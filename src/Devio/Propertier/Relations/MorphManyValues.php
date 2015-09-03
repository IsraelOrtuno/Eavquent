<?php
namespace Devio\Propertier\Relations;

use Devio\Propertier\PropertyBuilder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyValues extends MorphMany
{
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        $results = $this->query->get();
        $properties = $this->parent->properties;
        $factory = new PropertyBuilder();

        // Now we are mapping the relationship to transform any PropertyValue
        // model into the right TypeProperty object. This way has been the
        // easier for performing this task. This might change in future.
        return $results->map(function ($item) use ($factory, $properties)
        {
            $property = $properties->find($item->property_id);

            return $factory->make($property, $item->getAttributes());
        });
    }
}