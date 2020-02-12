<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransporters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_transporters', function (Blueprint $table) {
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('guarantor_name')->nullable();
            $table->text('guarantor_address')->nullable();
            $table->string('guarantor_phone_no')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->text('next_of_kin_address')->nullable();
            $table->string('next_of_kin_phone_no')->nullable();
            $table->string('next_of_kin_relationship')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_transporters', function (Blueprint $table) {
            $table->dropColumn('bank_name');
            $table->dropColumn('account_name');
            $table->dropColumn('account_number');
            $table->dropColumn('guarantor_name');
            $table->dropColumn('guarantor_address');
            $table->dropColumn('guarantor_phone_no');
            $table->dropColumn('next_of_kin_name');
            $table->dropColumn('next_of_kin_address');
            $table->dropColumn('next_of_kin_phone_no');
            $table->dropColumn('next_of_kin_relationship');
        });
    }
}
