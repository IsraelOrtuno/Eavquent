<?php

namespace Devio\Eavquent;

class EavquentManager
{
    /**
     * @var Getter
     */
    protected $getter;

    /**
     * @var Setter
     */
    protected $setter;

    /**
     * Manager constructor.
     *
     * @param Getter $getter
     * @param Setter $setter
     */
    public function __construct(Getter $getter, Setter $setter)
    {
        $this->getter = $getter;
        $this->setter = $setter;
    }
}
