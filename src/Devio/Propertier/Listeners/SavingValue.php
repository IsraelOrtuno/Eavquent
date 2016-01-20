<?php

namespace Devio\Propertier\Listeners;

use Devio\Propertier\Exceptions\EntityNotFoundException;

class SavingValue
{
    /**
     * Handling before saving.
     *
     * @param $model
     * @return bool
     * @throws EntityNotFoundException
     */
    public function handle($model)
    {
        $foreignKey = 'entity_id'; // $model->entity()->getForeignKey();

        // We will stop checking if there is any entity id set into the foreign
        // key that corresponds to the polymorphic relation. If this field is
        // null, we'll have to manually set it to the value parent entity.
        if (! is_null($model->getAttribute($foreignKey))) {
            return true;
        }

        // In case the entity was no existing before creating this value, we have
        // to manually set the entity id of the polymorphic relationship or we
        // would store a value model that would belong to an unknown entity.
        if (! is_null($entity = $model->getEntity())) {
            $model->setAttribute($foreignKey, $entity->getKey());
            return true;
        }

        throw new EntityNotFoundException;
    }
}