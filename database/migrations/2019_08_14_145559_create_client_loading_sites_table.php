<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientLoadingSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_client_loading_sites', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('state_id')->unsigned();
            $table->integer('loading_site_id')->unsigned();
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
        Schema::dropIfExists('tbl_kaya_client_loading_sites');
    }
}
