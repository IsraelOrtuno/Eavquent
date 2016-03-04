<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEavValuesVarcharTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(eav_value_table('varchar'), function (Blueprint $table)
        {
            $table->increments('id');

            $table->string('value');
            $table->integer('attribute_id');

            $table->string('entity_type');
            $table->integer('entity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(eav_value_table('varchar'));
    }
}
