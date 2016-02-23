<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 30);
            $table->string('name', 30);
            $table->boolean('multivalue')->default(false);
            $table->string('partner');
            $table->text('default_value')->nullable();

            $table->timestamps();

            $table->index('partner');

            $table->unique(['partner', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fields');
    }
}
