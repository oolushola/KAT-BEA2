<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_company_profiles', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('company_name');
            $table->string('company_email');
            $table->string('website');
            $table->string('company_phone_no');
            $table->text('address');
            $table->string('company_logo');
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_no');
            $table->string('tin');
            $table->integer('authorized_user_id')->unsigned();
            $table->string('signatory');
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
        Schema::dropIfExists('tbl_kaya_company_profiles');
    }
}
