<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVoucherDescWithAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_payment_voucher_descs', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('amount');
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
            $table->dropColumn('attachment');
        });
    }
}
