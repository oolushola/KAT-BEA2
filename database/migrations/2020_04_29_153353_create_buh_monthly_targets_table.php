<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuhMonthlyTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_buh_monthly_targets', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('current_year');
            $table->string('current_month');
            $table->double('target', 15, 2);
            $table->integer('user_id')->unsigned();
            $table->float('average_rating')->nullable();
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
        Schema::dropIfExists('tbl_kaya_buh_monthly_targets');
    }
}
