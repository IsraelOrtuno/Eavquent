<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEavAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(eav_table('attributes'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('label');
            $table->string('model');
            $table->string('entity');
            $table->boolean('collection')->default(false);
            $table->text('default_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(eav_table('attributes'));
    }
}
