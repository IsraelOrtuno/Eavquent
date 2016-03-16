<?php

namespace Devio\Eavquent;

class ReadQuery extends Query
{
    /**
     * Read the content of an attribute.
     *
     * @param $key
     * @return mixed|void
     */
    public function read($key)
    {
        if ($this->isGetRawAttributeMutator($key)) {
            return $this->getRawContent($key);
        }

        return $this->getContent($key);
    }

    /**
     * Get the content of the given attribute.
     *
     * @param $key
     */
    public function getContent($key)
    {
        $value = $this->getRawContent($key);
        $attribute = $this->getAttribute($key);

        // In case we are accessing to a multivalued attribute, we will return
        // a collection with pairs of id and value content. Otherwise we'll
        // just return the single model value content as a plain result.
        if ($attribute->isCollection()) {
            return $value->pluck('content', 'id');
        }

        return $value->getContent();
    }

    /**
     * Check if the key corresponds to an attribute.
     *
     * @param $key
     * @return mixed
     */
    public function isAttribute($key)
    {
        $key = $this->clearGetRawAttributeMutator($key);

        return parent::isAttribute($key);
    }

    /**
     * Get the raw content of the attribute (raw relationship).
     *
     * @param $key
     * @return mixed
     */
    protected function getRawContent($key)
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