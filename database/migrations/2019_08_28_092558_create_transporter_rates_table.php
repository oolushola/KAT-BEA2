<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransporterRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_transporter_rates', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('transporter_from_state_id')->unsigned();
            $table->integer('transporter_to_state_id')->unsigned();
            $table->string('transporter_destination');
            $table->integer('transporter_tonnage')->unsigned();
            $table->double('transporter_amount_rate');
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
        Schema::dropIfExists('tbl_kaya_transporter_rates');
    }
}
