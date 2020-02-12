<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceSubheadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_invoice_subheadings', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('sales_order_no_header');
            $table->string('invoice_no_header');
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
        Schema::dropIfExists('tbl_kaya_invoice_subheadings');
    }
}
