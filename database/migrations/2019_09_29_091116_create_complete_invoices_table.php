<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompleteInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_complete_invoices', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('trip_id')->unsigned();
            $table->integer('invoice_no');
            $table->string('completed_invoice_no');
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
        Schema::dropIfExists('tbl_kaya_complete_invoices');
    }
}
