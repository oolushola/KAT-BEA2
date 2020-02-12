<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTruckAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_truck_availabilities', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('loading_site_id')->unsigned();
            $table->integer('truck_id')->unsigned();
            $table->integer('transporter_id')->unsigned();
            $table->integer('driver_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('destination_state_id')->unsigned();
            $table->string('exact_location_id');
            $table->text('truck_status');
            $table->integer('reported_by');
            $table->boolean('status')->default(false);
            $table->string('dated');
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
        Schema::dropIfExists('tbl_kaya_truck_availabilities');
    }
}
