<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_trip_payments', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('trip_id')->unsigned();
            $table->integer('transporter_rate_id')->unsigned();
            $table->double('amount');
            $table->double('standard_advance_rate');
            $table->double('standard_balance_rate');
            $table->enum('exception', ['1', '2', '3', '4', '5'])->default(1);
            $table->boolean('advance_paid')->default(false);
            $table->boolean('balance_paid')->default(false);
            $table->double('advance');
            $table->double('balance')->nullable();            
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('trip_payments');
    }
}
