<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableInvoiceWithPoNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_complete_invoices', function (Blueprint $table) {
            $table->string('po_number')->nullable()->after('amount_paid_dfferent');
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
            $table->dropColumn('po_number');
        });
    }
}
