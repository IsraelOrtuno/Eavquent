<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_values', function (Blueprint $table)
        {
            $table->increments('id');

            $table->text('value');
            $table->integer('field_id');

            $table->string('partner_type');
            $table->integer('partner_id');

            $table->timestamps();
            
            $table->index(['field_id', 'partner_id']);
            $table->index(['partner_type', 'partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('field_values');
    }
}
