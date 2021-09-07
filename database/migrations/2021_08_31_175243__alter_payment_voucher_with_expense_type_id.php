<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentVoucherWithExpenseTypeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_payment_voucher_descs', function (Blueprint $table) {
            $table->string('expense_type')->after('attachment');
            $table->integer('expense_type_id')->after('expense_type')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_payment_voucher_descs', function (Blueprint $table) {
            $table->dropColumn(['expense_type', 'expense_type_id']);
        });
    }
}
