<?php namespace Devio\Propertier\Relations;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class MorphManyValues extends MorphMany
{
    use TransformDictionary;
    
//    public function getResults()
//    {
//        $results = $this->query->get();
//
//        dd($results);
//    }
}