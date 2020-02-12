<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_trip_events', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('trip_id')->unsigned();
            $table->date('current_date');
            $table->boolean('journey_status')->default(0);
            $table->string('location_check_one')->nullable();
            $table->string('location_one_comment')->nullable();
            $table->string('location_check_two')->nullable();
            $table->string('location_two_comment')->nullable();
            $table->boolean('destination_status')->default(0);
            $table->string('time_arrived_destination')->nullable();
            $table->boolean('offloading_status')->default(0);
            $table->string('offload_start_time')->nullable();
            $table->string('offload_end_time')->nullable();
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
        Schema::dropIfExists('trip_events');
    }
}
