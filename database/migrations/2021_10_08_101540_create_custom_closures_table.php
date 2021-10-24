<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_closures', function (Blueprint $table) {
            $table->id();
            $table->date('from');
            $table->date('to');
            $table->string('name');
            $table->string('color');
            $table->longText('details')->nullable();
            $table->unsignedBigInteger('time_slots_id');
            $table->foreign('time_slots_id')->references('id')->on('time_slots');
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
        Schema::dropIfExists('custom_closures');
    }
}
