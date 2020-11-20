<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransloadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_trip_transloaders', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('trip_id')->unsigned();
            $table->integer('previous_truck_id')->unsigned();
            $table->integer('previous_driver_id')->unsigned();
            $table->integer('transloaded_truck_id')->unsigned();
            $table->integer('transloaded_driver_id')->unsigned();
            $table->text('reason_for_transloading');
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
        Schema::dropIfExists('tbl_kaya_trip_transloaders');
    }
}
