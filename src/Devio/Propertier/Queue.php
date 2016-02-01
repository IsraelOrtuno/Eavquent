<?php

namespace Devio\Propertier;

use Illuminate\Support\Collection;

class Queue
{
    /**
     * Deletion queue.
     *
     * @var Collection
     */
    protected $deleteQueue;

    /**
     * Queue constructor.
     */
    public function __construct()
    {
        $this->deleteQueue = new Collection;
    }

    /**
     * Process the queue.
     */
    public function process()
    {
        $values = $this->deleteQueue->pluck('id');

        if ($values->count()) {
            Value::whereIn('id', $values)->delete();
        }
    }

    /**
     * Add a new item to the queue.
     *
     * @param $item
     */
    public function add(Value $item)
    {
        if ($item->exists) {
            $this->deleteQueue->push($item);
        }
    }

    /**
     * Flushes the queue.
     */
    public function flush()
    {
        $this->deleteQueue = new Collection;
    }
}