<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulkPaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_bulk_payment_histories', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('bulk_payment_id')->unisgned();
            $table->integer('transporter_id')->unsigned();
            $table->double('amount_credited');
            $table->string('date_uploaded')->nullable();
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
        Schema::dropIfExists('tbl_kaya_bulk_payment_histories');
    }
}
