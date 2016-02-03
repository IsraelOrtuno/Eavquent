<?php

namespace Devio\Propertier;

use Illuminate\Support\Collection;

class Queue
{
    /**
     * Queued items.
     *
     * @var Collection
     */
    protected $queue;

    /**
     * Queue constructor.
     */
    public function __construct()
    {
        $this->queue = new Collection;
    }

    /**
     * Process the queue.
     *
     * @return mixed
     */
    public function process()
    {
        $values = $this->queue->pluck('id');

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
            $this->queue->push($item);
        }
    }

    /**
     * Flushes the queue.
     */
    public function flush()
    {
        $this->queue = new Collection;
    }
}