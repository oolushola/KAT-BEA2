<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransporterWithCompletedRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_transporters', function (Blueprint $table) {
            $table->string('registration_completed')->nullable()->after('next_of_kin_relationship');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_transporters', function (Blueprint $table) {
            $table->dropColumn('registration_completed')->after('next_of_kin_relationship');
        });
    }
}
