<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientFareRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_client_fare_rates', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('from_state_id')->unsigned();
            $table->integer('to_state_id')->unsigned();
            $table->string('destination');
            $table->enum('exception', ['0', '1'])->default(0);
            $table->float('exception_amount')->nullable();
            $table->integer('tonnage');
            $table->float('amount_rate');
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
        Schema::dropIfExists('tbl_kaya_client_fare_rates');
    }
}
