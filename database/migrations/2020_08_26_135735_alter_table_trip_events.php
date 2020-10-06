<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTripEventDestinations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trip_events', function (Blueprint $table) {
            $table->string('gate_in_time_destination')->after('time_arrived_destination')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_trip_events', function (Blueprint $table) {
            $table->dropColumn('gate_in_time_destination');
        });
    }
}
