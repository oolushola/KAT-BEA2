<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTblPaymentVouchersWithVerifyBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_payment_vouchers', function (Blueprint $table) {
            $table->integer('hod')->after('requested_by')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_payment_vouchers', function (Blueprint $table) {
            $table->integer('hod')->after('requested_by')->unsigned();
        });
    }
}
