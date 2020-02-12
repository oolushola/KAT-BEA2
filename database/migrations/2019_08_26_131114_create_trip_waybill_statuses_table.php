<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripWaybillStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_trip_waybill_statuses', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('trip_id')->unsigned();
            $table->boolean('waybill_status');
            $table->text('comment');
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
        Schema::dropIfExists('tbl_kaya_trip_waybill_statuses');
    }
}
