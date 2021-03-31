<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOffloadWaybillStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_offload_waybill_remarks', function (Blueprint $table) {
            $table->string('container_card_no')->nullable()->after('waybill_remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_offload_waybill_remarks', function (Blueprint $table) {
           $table->dropColumn('container_card_no');
        });
    }
}
