<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_payment_notifications', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('trip_id');
            $table->double('amount', 15, 2);
            $table->string('payment_for');
            $table->integer('uploaded_by');
            $table->string('uploaded_at');
            $table->boolean('paid_status')->default(false);
            $table->string('paid_time_stamps')->nullable();
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
        Schema::dropIfExists('tbl_kaya_payment_notifications');
    }
}
