<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBulkPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_bulk_payments', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('transporter_id')->unsigned();
            $table->double('balance')->nullable();
            $table->double('amount_credited');
            $table->string('date_uploaded');
            $table->string('date_approved')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('approval_status')->default(0);
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
        Schema::dropIfExists('tbl_kaya_bulk_payments');
    }
}
