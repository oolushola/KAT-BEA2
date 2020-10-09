<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_staff_extras', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('guarantor_full_name')->nullable();
            $table->string('guarantor_phone_no')->nullable();
            $table->string('guarantor_address')->nullable();
            $table->string('nok_full_name')->nullable();
            $table->string('nok_phone_no')->nullable();
            $table->string('nok_address')->nullable();
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
        Schema::dropIfExists('tbl_kaya_staff_extras');
    }
}
