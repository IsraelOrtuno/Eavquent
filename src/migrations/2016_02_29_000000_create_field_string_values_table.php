<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldStringValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Value::TABLE_PREFIX . 'string', function (Blueprint $table)
        {
            $table->increments('id');

            $table->string('value');
            $table->integer('attribute_id');

            $table->string('entity_type');
            $table->integer('entity_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(Value::TABLE_PREFIX . 'string');
    }
}
