<?php

namespace Devio\Eavquent\Agnostic;

use Illuminate\Config\Repository;

class ConfigRepository extends Repository
{
    /**
     * The singleton instance.
     *
     * @var static
     */
    protected static $instance;

    /**
     * ConfigRepository constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $items = array_merge(
            require __DIR__ . '/../../config.php', $items
        );

        parent::__construct($items);
    }

    /**
     * Get the config repository instance.
     *
     * @param array $items
     * @return static
     */
    public static function getInstance(array $items = [])
    {
        if (is_null(static::$instance)) {
            return static::$instance = new static($items);
        }

        $items = array_merge(static::$instance->all(), $items);

        static::$instance->setItems($items);

        return static::$instance;
    }

    /**
     * Set the items array.
     *
     * @param array $items
     */
    public function setItems(array $items = [])
    {
        $this->items = $items;
    }
}
