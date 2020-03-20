<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCompleteInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_complete_invoices', function (Blueprint $table) {
            $table->float('vat_used')->after('date_paid')->nullable();
            $table->float('withholding_tax_used')->after('vat_used')->nullable();
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
            $table->dropColumn('vat_used');
            $table->dropColumn('withholding_tax_used');
        });
    }
}
