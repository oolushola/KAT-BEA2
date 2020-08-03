<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_clients', function (Blueprint $table) {
            $table->string('bank_name_payment')->nullable()->before('created_at');
            $table->string('account_name_payment')->nullable()->after('bank_name_payment');
            $table->string('account_no_payment')->nullable()->after('account_name_payment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_clients', function (Blueprint $table) {
            $table->dropColumn(['bank_name_payment', 'account_name_payment', 'account_no_payment']);
        });
    }
}
