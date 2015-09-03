<?php
namespace Devio\Propertier\Properties;

class StringProperty extends PropertyAbstract
{
    /**
     * Decorating before showing.
     *
     * @return string
     */
    public function decorate()
    {
        return (string) $this->plainValue;
    }

    /**
     * Forcing string casting.
     *
     * @param $plainValue
     *
     * @return string
     */
    protected function cast($plainValue)
    {
        return (string) $plainValue;
    }

    /**
     * Validating it is a valid string.
     *
     * @return bool
     */
    public function isValidForStorage()
    {
        return is_string($this->plainValue);
    }
}