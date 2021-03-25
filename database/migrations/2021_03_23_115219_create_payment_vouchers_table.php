<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_payment_vouchers', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('uniqueId');
            $table->integer('requested_by')->unsigned();
            $table->string('request_timestamps');
            $table->boolean('check_status')->default(0);
            $table->integer('checked_by')->nullable();
            $table->string('checked_timestamps')->nullable();
            $table->boolean('upload_status')->default(0);
            $table->integer('upload_by')->nullable();
            $table->string('upload_timestamps')->nullable();
            $table->boolean('approved_status')->default(0);
            $table->integer('approved_by')->nullable();
            $table->string('approval_timestamps')->nullable();
            $table->boolean('voucher_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_kaya_payment_vouchers');
    }
}
