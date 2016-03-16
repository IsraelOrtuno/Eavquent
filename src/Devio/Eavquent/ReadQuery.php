<?php

namespace Devio\Eavquent;

class ReadQuery extends Query
{
    /**
     * @param $key
     * @return mixed|void
     */
    public function read($key)
    {
        if ($this->isGetRawAttributeMutator($key)) {
            return $this->getRawValue($key);
        }

        return $this->getValue($key);
    }

    /**
     * @param $key
     */
    public function getValue($key)
    {
        $value = $this->getRawValue($key);
//        $attribute = $this->getAttribute($key);

        // In case we are accessing to a multivalued attribute, we will return
        // a collection with pairs of id and value content. Otherwise we'll
        // just return the single model value content as a plain result.
//        if ($attribute->isMultivalue()) {
//            return $value->pluck('content', $value->getKey());
//        }

        return $value->getAttribute('content');
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getRawValue($key)
    {
        $key = $this->clearGetRawAttributeMutator($key);

        return $this->entity->getRelationValue($key);
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string $key
     * @return bool
     */
    protected function isGetRawAttributeMutator($key)
    {
        return (bool) preg_match('/^raw(\w+)object$/i', $key);
    }

    /**
     * Remove any mutator prefix and suffix.
     *
     * @param $key
     * @return mixed
     */
    protected function clearGetRawAttributeMutator($key)
    {
        return $this->isGetRawAttributeMutator($key) ?
            camel_case(str_ireplace(['raw', 'object'], ['', ''], $key)) : $key;
    }
}