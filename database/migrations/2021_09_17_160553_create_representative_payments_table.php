<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentativePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('representative_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_history_id');
            $table->unsignedBigInteger('rep_id');
            $table->integer('fee');
            $table->foreign('coupon_history_id')->references('id')->on('coupon_histories');
            $table->foreign('rep_id')->references('id')->on('users');
            $table->boolean('payment_status')->default(0);
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
        Schema::dropIfExists('representative_payments');
    }
}
