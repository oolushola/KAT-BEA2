<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTripPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trip_payments', function (Blueprint $table) {
            $table->integer('transporter_id')->unsigned()->after('transporter_rate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_trip_payments', function (Blueprint $table) {
            $table->dropColumn('transporter_id');
        });
    }
}
