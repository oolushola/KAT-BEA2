<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_clients', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->enum('client_status', ['0', '1'])->default(1);
            $table->string('company_name');
            $table->string('person_of_contact');
            $table->string('phone_no');
            $table->string('email');
            $table->integer('country_id')->unsigned();
            $table->integer('state_id')->unsigned();
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
        Schema::dropIfExists('tbl_kaya_clients`');
    }
}
