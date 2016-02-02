<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_values', function (Blueprint $table)
        {
            $table->increments('id');

            $table->text('value');
            $table->integer('property_id');

            $table->string('entity_type');
            $table->integer('entity_id');

            $table->timestamps();
            
            $table->index(['property_id', 'entity_id']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('property_values');
    }
}
