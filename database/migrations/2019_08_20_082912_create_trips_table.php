<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_trips', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('trip_id')->nullable();
            $table->string('gate_in');
            $table->integer('loading_site_id')->unsigned();
            $table->integer('transporter_id')->unsigned();
            $table->integer('truck_id')->unsigned();
            $table->integer('driver_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('destination_state_id')->unsigned();
            $table->integer('exact_location_id')->unsigned();
            $table->string('account_officer');
            $table->string('arrival_at_loading_bay')->nullable();
            $table->string('loading_start_time')->nullable();
            $table->string('loading_end_time')->nullable();
            $table->string('departure_date_time')->nullable();
            $table->string('gated_out')->nullable();
            $table->string('customers_name')->nullable();
            $table->string('customer_no')->nullable();
            $table->integer('loaded_quantity')->nullable();
            $table->integer('loaded_weight')->nullable();
            $table->text('customer_address')->nullable();
            $table->integer('tracker')->nullable();
            $table->boolean('trip_status')->nullable();
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
        Schema::dropIfExists('tbl_kaya_trips');
    }
}
