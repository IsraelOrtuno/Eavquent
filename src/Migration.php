<?php

namespace Devio\Eavquent;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration as DatabaseMigration;

abstract class Migration extends DatabaseMigration
{
    /**
     * The table name.
     *
     * @return mixed
     */
    abstract protected function tableName();

    /**
     * The content column.
     *
     * @param Blueprint $table
     * @param $name
     * @return void
     */
    abstract protected function contentColumn(Blueprint $table, $name);

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create(eav_value_table($this->tableName()), function (Blueprint $table) {
            $table->increments('id');

            $this->contentColumn($table, 'content');

            $table->integer('attribute_id')->unsigned();

            $table->string('entity_type');
            $table->integer('entity_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop(eav_value_table($this->tableName()));
    }
}
