<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTrips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trips', function (Blueprint $table) {
            $table->integer('day')->unsigned();
            $table->string('month');
            $table->string('year');
            $table->integer('user_id')->unsigned();
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
            $table->dropColumn('day');
            $table->dropColumn('month');
            $table->dropColumn('year');
            $table->dropColumn('user_id');
        });
    }
}
