<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargoAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_cargo_availabilities', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('current_year');
            $table->string('current_month');
            $table->integer('client_id')->unsigned();
            $table->integer('available_order')->unsigned();
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
        Schema::dropIfExists('tbl_kaya_cargo_availabilities');
    }
}
