<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadingClerksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_loading_clerks', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_no');
            $table->string('email');
            $table->integer('location_id')->unsigned();
            $table->enum('field_ops_type', ['1', '2'])->default(1);
            $table->text('address');
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
        Schema::dropIfExists('tbl_kaya_loading_clerks');
    }
}
