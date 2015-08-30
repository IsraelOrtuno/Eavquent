<?php
namespace Devio\Propertier\Properties;

use Devio\Propertier\Models\PropertyChoice;
use Devio\Propertier\Exceptions\PropertyChoiceNotFound;

class ChoiceProperty extends PropertyAbstract
{

    /**
     * Casting to an integer value.
     *
     * @param $plainValue
     * @return mixed
     */
    protected function cast($plainValue)
    {
        return (int) $plainValue;
    }

    /**
     * Decorate the single value before returned.
     *
     * @return array
     */
    public function decorate()
    {
        //        $this->isValidForStorage();
        return [
            'id'    => $this->plainValue,
            'value' => PropertyChoice::find($this->plainValue)->value
        ];
    }

    /**
     * Will check if the value really exists into the property choices
     * list. If so, will pass, otherwise it won't allow.
     */
    public function isValidForStorage()
    {
        if ( ! $choice = PropertyChoice::find($this->plainValue))
        {
            throw new PropertyChoiceNotFound;
        }

        return true;
    }
}