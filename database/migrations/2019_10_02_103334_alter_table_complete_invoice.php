<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCompleteInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_complete_invoices', function (Blueprint $table) {
            $table->boolean('paid_status')->default(false)->after('completed_invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_kaya_complete_invoices', function (Blueprint $table) {
            $table->dropColumn('paid_status');
        });
    }
}
