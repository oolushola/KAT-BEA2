<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKayaChasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_chases', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('chase_id');
            $table->integer('truck_id');
            $table->integer('transporter_id');
            $table->integer('driver_id');
            $table->date('chase_start_date');
            $table->date('eta');
            $table->string('preffered_loading_site');
            $table->string('preffered_destination');
            $table->string('remark')->nullable();
            $table->boolean('push_status')->default(FALSE);
            $table->boolean('pop_status')->default(FALSE);
            $table->string('profiled_by');
            $table->string('last_updated_by');
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
        Schema::dropIfExists('tbl_kaya_chases');
    }
}
