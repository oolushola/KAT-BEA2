<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_drivers', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('driver_first_name');
            $table->string('driver_last_name');
            $table->string('driver_phone_number');
            $table->string('motor_boy_first_name');
            $table->string('motor_boy_last_name');
            $table->string('motor_boy_phone_no');
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
        Schema::dropIfExists('drivers');
    }
}
