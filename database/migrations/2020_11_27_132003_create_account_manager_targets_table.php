<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountManagerTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_account_manager_targets', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('current_year')->unsigned();
            $table->integer('current_month')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('target')->unsigned();
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
        Schema::dropIfExists('tbl_kaya_account_manager_targets');
    }
}
