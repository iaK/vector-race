<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races', function (Blueprint $table) {
            $table->increments('id');
            $table->text('participant_data')->nullable();
            $table->string("status")->default("lobby");
            $table->integer("user_turn_id")->nullable();
            $table->integer("course_id");
            $table->integer("winner_id")->nullable();
            $table->integer("host_id");
            $table->integer("skipped")->default(0);
            $table->integer("moves")->default(0);
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
        Schema::dropIfExists('races');
    }
}
