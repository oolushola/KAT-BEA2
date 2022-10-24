<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTruckAvailabilityWithPayout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_truck_availabilities', function (Blueprint $table) {
            $table->integer("payout")->after("dated")->nullable()->unsigned();
            $table->boolean("authorize")->after("payout")->default(FALSE);
            $table->integer("authorized_by")->after("authorize")->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_truck_availabilities', function (Blueprint $table) {
            $table->dropColumn([
                'payout',
                'authorize',
                'authorized_by'
            ]);
        });
    }
}
