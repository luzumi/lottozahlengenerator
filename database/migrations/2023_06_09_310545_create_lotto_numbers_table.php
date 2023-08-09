<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLottonumbersTable extends Migration
{
    public function up()
    {
        Schema::create('lotto_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('drawing_id');
            $table->string('number_one');
            $table->string('number_two');
            $table->string('number_three');
            $table->string('number_four');
            $table->string('number_five');
            $table->string('number_six');
            $table->string('superzahl')->nullable();
            $table->string('zusatzzahl')->nullable();
            $table->timestamps();

            $table->foreign('drawing_id')->references('id')->on('draws')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lotto_numbers');
    }
}
