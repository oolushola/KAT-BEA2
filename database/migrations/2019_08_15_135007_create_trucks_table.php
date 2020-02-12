<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrucksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_trucks', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('transporter_id')->unsigned();
            $table->integer('truck_type_id')->unsigned();
            $table->string('truck_no');
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
        Schema::dropIfExists('tbl_kaya_trucks');
    }
}
