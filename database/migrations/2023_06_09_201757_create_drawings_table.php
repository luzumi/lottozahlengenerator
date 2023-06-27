<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrawingsTable extends Migration
{
    public function up()
    {
        Schema::create('draws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('year');
            $table->string('draw_date');
            $table->string('calendar_week');
            $table->string('draw_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('draws');
    }
}
