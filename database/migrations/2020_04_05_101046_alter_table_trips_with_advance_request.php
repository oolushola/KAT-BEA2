<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTripsWithAdvanceRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_trips', function (Blueprint $table) {
            $table->boolean('advance_request')->default(FALSE)->after('transporter_rate');
            $table->boolean('advance_paid')->default(0)->after('advance_request');
            $table->integer('advance_requested_by')->nullable()->after('advance_paid');
            $table->boolean('balance_request')->default(FALSE)->after('advance_requested_by');
            $table->integer('balance_requested_by')->nullable()->after('advance_requested_by');
            $table->integer('outstanding_payment')->nullable()->after('balance_requested_by');
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
            $table->dropColumn('advance_request')->after('transporter_rate');
            $table->dropColumn('advance_paid')->after('advance_request');
            $table->dropColumn('advance_requested_by')->after('advance_paid');
            $table->dropColumn('balance_request')->after('advance_requested_by');
            $table->dropColumn('balance_requested_by')->after('advance_requested_by');
            $table->dropColumn('outstanding_payment')->after('balance_requested_by');
        });
    }
}
