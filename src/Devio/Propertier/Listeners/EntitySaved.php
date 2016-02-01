<?php

namespace Devio\Propertier\Listeners;

use Exception;
use Illuminate\Database\Connection;

class EntitySaved
{
    /**
     * Connection instance.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * EntitySaved constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Handling after saved.
     *
     * @param $model
     * @return bool
     * @throws Exception
     */
    public function handle($model)
    {
        if (! $model->relationLoaded('properties')) {
            return true;
        }

        try {
            // When an entity is saved, we will iterate every property and perform
            // a push in every of them. Setting the entity of each property will
            // prevent us of trying to save a value for a not known entity id.
            foreach ($model->properties as $property) {
                $property->getQueue()->process();

                $property->entity($model)->push();
            }

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();

            throw $e;
        }
    }
}