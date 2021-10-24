<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationSlotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_slot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservations_id');
            $table->unsignedBigInteger('time_slots_id');
            $table->foreign('reservations_id')->references('id')->on('reservations');
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
        Schema::dropIfExists('reservation_slot');
    }
}
