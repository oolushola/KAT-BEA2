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
            $table->enum('parent_company_status', ['0', '1'])->default(0)->before('client_status');
            $table->integer('parent_company_id')->unsigned()->after('parent_company_status');
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
            $table->dropColumn('parent_company_status');
            $table->dropColumn('parent_company_id');
        });
    }
}
