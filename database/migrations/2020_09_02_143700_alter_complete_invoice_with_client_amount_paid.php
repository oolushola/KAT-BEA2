<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompleteInvoiceWithClientAmountPaid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_kaya_complete_invoices', function (Blueprint $table) {
            $table->boolean('amount_paid_dfferent')->default(0)->after('withholding_tax_used');
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
            $table->dropColumn('amount_paid_dfferent');
        });
    }
}
