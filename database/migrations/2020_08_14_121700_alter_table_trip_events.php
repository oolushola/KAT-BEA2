<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTripEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trip_events', function (Blueprint $table) {
            $table->string("morning_lga")->after('location_one_comment');
            $table->integer('morning_issue_type')->nullable()->after('morning_lga');
            $table->string("afternoon_lga")->after('location_two_comment');
            $table->integer('afternoon_issue_type')->nullable()->after('afternoon_lga');
            $table->integer('offload_issue_type')->nullable();

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
            $table->dropColumn('morning_lga');
            $table->dropColumn('morning_issue_type');
            $table->dropColumn('afternoon_lga');
            $table->dropColumn('afternoon_issue_type');
            $table->dropColumn('offload_issue_type');
        });
    }
}
