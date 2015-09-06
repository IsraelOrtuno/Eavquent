<?php
namespace Devio\Propertier\Relations;

use Devio\Propertier\PropertyValue;
use Illuminate\Database\Eloquent\Collection;

trait TransformDictionary
{
    protected function buildDictionary(Collection $results)
    {
        $dictionary = [];

        $foreign = $this->getPlainForeignKey();

        // First we will create a dictionary of models keyed by the foreign key of the
        // relationship as this will allow us to quickly access all of the related
        // models without having to do nested looping which will be quite slow.
        foreach ($results as $result)
        {
            if ($result instanceof PropertyValue)
            {
                $result = $result->transformProperty();
            }

            $dictionary[$result->{$foreign}][] = $result;
        }

        return $dictionary;
    }
}