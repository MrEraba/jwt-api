<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesDaysAndCallendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // horarios

        Schema::create('callendars', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->date('start');
            $table->date('end');

            $table->timestamps();
        });

        Schema::create('days', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->index();
            $table->string('name');
            $table->integer('callendar_id')->index();
            $table->integer('hour');

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
        //
        Schema::drop('callendars');
        Schema::drop('days');
    }
}
