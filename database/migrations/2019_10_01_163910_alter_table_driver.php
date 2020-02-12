<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDriver extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_drivers', function (Blueprint $table) {
            $table->string('licence_no')->nullable()->after('driver_phone_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_drivers', function (Blueprint $table) {
            $table->dropColumn('licence_no');
        });
    }
}
