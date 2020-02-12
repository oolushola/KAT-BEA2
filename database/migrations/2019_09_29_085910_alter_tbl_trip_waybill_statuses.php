<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTblTripWaybillStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trip_waybill_statuses', function (Blueprint $table) {
            $table->boolean('invoice_status')->nullable()->default(false);
            $table->string('date_invoiced')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_trip_waybill_statuses', function (Blueprint $table) {
            $table->dropColumn('invoice_status');
            $table->dropColumn('date_invoiced');
        });
    }
}
