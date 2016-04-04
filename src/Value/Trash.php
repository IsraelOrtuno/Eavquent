<?php

namespace Devio\Eavquent\Value;

use Illuminate\Support\Collection as BaseCollection;

class Trash extends BaseCollection
{
    /**
     * Add values to the trash.
     *
     * @param $values
     */
    public function add($values)
    {
        foreach ((array) $values as $value) {
            if ($value->exists) {
                $this->push($value);
            }
        }
    }

    /**
     * Clear the trash and delete items from database.
     */
    public function clear()
    {
        if (! $this->count()) {
            return;
        }

        $class = get_class($this->first());

        // Fetching the first element class we will discover the model we will
        // use for deleting. Let's now delete all the values based on their
        // id. Once done we just have to reset the trash items to empty.
        $class::whereIn('id', $this->pluck('id'))->delete();

        $this->items = [];
    }
}
