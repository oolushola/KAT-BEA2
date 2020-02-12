<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTripsForFareRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trips', function (Blueprint $table) {
            $table->string('client_rate')->nullable()->after('user_id');
            $table->string('transporter_rate')->nullable()->after('client_rate');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_trips', function (Blueprint $table) {
            $table->dropColumn('client_rate');
            $table->dropColumn('transporter_rate');
        });
    }
}
