<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesBreakdownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_expenses_breakdowns', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('current_year');
            $table->integer('current_month');
            $table->string('category');
            $table->double('amount', 18, 5);
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
        Schema::dropIfExists('tbl_kaya_expenses_breakdowns');
    }
}
