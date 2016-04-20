<?php

use Devio\Eavquent\Migration;

class CreateEavValuesTextTable extends Migration
{
    /**
     * The table name.
     *
     * @return mixed
     */
    protected function tableName()
    {
        return 'text';
    }

    /**
     * The content column.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param $name
     * @return void
     */
    protected function contentColumn(\Illuminate\Database\Schema\Blueprint $table, $name)
    {
        $table->text($name);
    }
}
