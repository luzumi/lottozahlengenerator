<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWinningsTable extends Migration
{
    public function up()
    {
        Schema::create('winnings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('drawing_id');
            $table->unsignedBigInteger('winning_grade_id');
            $table->string('winners');
            $table->string('payout');
            $table->timestamps();

            $table->foreign('drawing_id')->references('id')->on('draws')->onDelete('cascade');
            $table->foreign('winning_grade_id')->references('id')->on('winning_grades')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('winners');
    }
}
