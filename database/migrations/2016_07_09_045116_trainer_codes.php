<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrainerCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainer_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trainer_id')->unsigned();
            $table->foreign('trainer_id')->references('id')->on('trainers');
            $table->string('codes');
            $table->string('expires');
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
        Schema::drop('trainer_codes');
    }
}
