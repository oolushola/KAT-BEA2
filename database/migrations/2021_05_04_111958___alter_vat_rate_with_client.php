<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVatRateWithClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_vat_rates', function (Blueprint $table) {
            $table->integer('client_id')->before('withholding_tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_vat_rates', function (Blueprint $table) {
            $table->dropColumn('client_id')->before('withholding_tax');
        });
    }
}
