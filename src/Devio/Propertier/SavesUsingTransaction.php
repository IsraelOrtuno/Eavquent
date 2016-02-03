<?php

namespace Devio\Propertier;

use Throwable;

trait SavesUsingTransaction
{
    /**
     * Use transaction while saving.
     *
     * @var bool
     */
    protected $savingTransaction = true;

    /**
     * Save the model to the database.
     *
     * @param array $options
     * @return mixed
     * @throws Throwable
     */
    public function save(array $options = [])
    {
        if ($this->savingTransaction) {
            return $this->saveWithinTransaction($options);
        }

        return parent::save($options);
    }

    /**
     * Saves the model into database wrapping queries into a transaction.
     *
     * @param array $options
     * @return mixed
     * @throws Throwable
     */
    public function saveWithinTransaction(array $options = [])
    {
        with($connection = $this->getConnection())->beginTransaction();

        // Begin the transaction before saving. If anything goes unexpectedly bad
        // during the saving and blocks it, we may roll it back to the original
        // state. Also improve performance when inserting/updating many rows.
        try {
            if ($saved = parent::save($options)) {
                $connection->commit();
            } else {
                $connection->rollBack();
            }

            return $saved;
        } catch (Throwable $e) {
            $connection->rollBack();

            throw  $e;
        }
    }

    /**
     * Enable transaction wrapping while saving.
     *
     * @return $this
     */
    public function enableSavingTransaction()
    {
        $this->savingTransaction = true;

        return $this;
    }

    /**
     * Disable transaction wrapping while saving.
     *
     * @return $this
     */
    public function disableSavingTransaction()
    {
        $this->savingTransaction = false;

        return $this;
    }
}