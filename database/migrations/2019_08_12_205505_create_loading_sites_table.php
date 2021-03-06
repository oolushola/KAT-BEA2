<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoadingSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_loading_sites', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('state_domiciled')->unsigned();
            $table->string('loading_site_code');
            $table->string('loading_site');
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
        Schema::dropIfExists('tbl_kaya_loading_sites');
    }
}
