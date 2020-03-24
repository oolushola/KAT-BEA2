<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceSpecialRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_invoice_special_remarks', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('condition');
            $table->string('invoice_no');
            $table->text('description');
            $table->float('amount');
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
        Schema::dropIfExists('tbl_kaya_invoice_special_remarks');
    }
}
