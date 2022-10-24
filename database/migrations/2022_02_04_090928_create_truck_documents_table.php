<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTruckDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_truck_documents', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('uploaded_by')->unsigned();
            $table->integer('truck_id')->unsigned();
            $table->string('vehicle_licence');
            $table->date('vehicle_licence_expiry');
            $table->string('roadworthiness');
            $table->date('roadworthiness_expiry');
            $table->string('insurance');
            $table->date('insurance_expiry');
            $table->string('proof_of_ownership');
            $table->date('poo_expiry');
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
        Schema::dropIfExists('tbl_kaya_truck_documents');
    }
}
