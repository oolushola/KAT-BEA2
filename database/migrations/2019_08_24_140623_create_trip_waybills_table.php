<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripWaybillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_trip_waybills', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('trip_id')->unsigned();
            $table->boolean('waybill_status');
            $table->string('remark')->nullable();
            $table->string('sales_order_no')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('approve_waybill')->nullable();
            $table->string('moment_uploaded')->nullable();
            $table->string('moment_approved')->nullable();
            $table->boolean('invoice_status')->nullable()->default(0);
            $table->string('invoice_no')->nullable();
            $table->string('date_invoiced')->nullable();
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
        Schema::dropIfExists('tbl_kaya_trip_waybills');
    }
}
