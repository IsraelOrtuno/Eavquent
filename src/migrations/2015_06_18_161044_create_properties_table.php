<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table)
        {
            $table->increments('id');

            $table->string('type', 30);
            $table->string('name', 30);

            $table->boolean('multivalue')->default(false);

            $table->boolean('choices')->default(false);

            $table->integer('min_length');
            $table->integer('max_length');

            $table->boolean('is_required');

            $table->string('entity');

            $table->text('default_value');

//            $table->boolean('searchable');

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
        Schema::drop('properties');
    }
}
