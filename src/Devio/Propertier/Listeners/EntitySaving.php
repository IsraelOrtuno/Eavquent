<?php

namespace Devio\Propertier\Listeners;


class EntitySaving
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
     * Handling before saving.
     *
     * @param $model
     */
    public function handle($model)
    {
        if ($model->relationLoaded('properties')) {
            $this->connection->beginTransaction();
        }
    }
}