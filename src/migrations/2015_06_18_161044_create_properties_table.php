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

            $table->boolean('is_required')->nullable();

            $table->string('entity');

            $table->text('default_value')->nullable();

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
