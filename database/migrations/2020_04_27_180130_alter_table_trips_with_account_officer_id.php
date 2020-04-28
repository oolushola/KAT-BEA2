<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTripsWithAccountOfficerId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trips', function (Blueprint $table) {
            $table->integer('account_officer_id')->after('account_officer')->nullable();
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
            $table->dropColumn('account_officer_id');
        });
    }
}
