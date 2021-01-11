<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOffloadStatusWithEmptyReturn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_offload_waybill_statuses', function (Blueprint $table) {
            $table->boolean('empty_returned')->DEFAULT(FALSE)->after('date_offloaded');
            $table->string('empty_returned_date')->nullable()->after('empty_returned');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_offload_waybill_statuses', function (Blueprint $table) {
            $table->dropColumn('empty_returned');
            $table->dropColumn('empty_returned_date');
        });
    }
}
