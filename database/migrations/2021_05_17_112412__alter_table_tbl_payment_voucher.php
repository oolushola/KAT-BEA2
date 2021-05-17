<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTblPaymentVoucher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_payment_vouchers', function (Blueprint $table) {
            $table->boolean('decline_status')->default(FALSE)->after('voucher_status');
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
            $table->dropColumn('decline_status');
        });
    }
}
