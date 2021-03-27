<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentVoucherDescsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_payment_voucher_descs', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('payment_voucher_id')->unsigned();
            $table->text('description');
            $table->string('owner')->nullable();
            $table->double('amount', 15, 2);
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
        Schema::dropIfExists('tbl_kaya_payment_voucher_descs');
    }
}
