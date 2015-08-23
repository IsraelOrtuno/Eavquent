<?php
namespace Devio\Propertier\Jobs;

use Devio\Propertier\Models\PropertyValue;
use Devio\Propertier\ValueSetter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClearPreviousValues extends Job implements SelfHandling, ShouldQueue
{

    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Deletes any element in the deletion queue.
     */
    public function handle()
    {
        $queue = $this->model->getValueDeletionQueue();
        // Will delete all the rows that matches any of the ids sterd in the
        // deletion queue variable. Will check for elements in this queue
        // to avoid performing a query if no element has to be deleted.
        if ($queue->count())
        {
            $deletionKeys = $queue->pluck('id')->toArray();
            PropertyValue::whereIn('id', $deletionKeys)
                         ->delete();
        }
    }
}