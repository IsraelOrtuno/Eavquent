<?php
namespace Devio\Propertier\Observers;

use Devio\Propertier\Models\PropertyChoice;

class PropertyChoiceObserver
{

    // NOTE: Esta clase debería encargarse de comprobar que cada vez que se
    // NOTE: elimina un PropertyChoice, se elimine de la lista de valores
    // NOTE: de la entidad a la que está asociada, evitando así que existan
    // NOTE: relaciones a choices que no existen.
    /**
     * @param PropertyChoice $choice
     */
    public function deleted(PropertyChoice $choice)
    {

    }
}