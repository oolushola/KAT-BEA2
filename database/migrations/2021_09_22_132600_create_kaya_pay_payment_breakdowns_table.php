<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKayaPayPaymentBreakdownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_kaya_pay_payment_breakdowns', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('kaya_pay_id');
            $table->integer('client_id')->unsigned();
            $table->string('loading_site');
            $table->date('gated_in');
            $table->string('truck_no');
            $table->string('driver_name')->nullable();
            $table->string('driver_phone_no')->nullable();
            $table->string('motor_boy_name')->nullable();
            $table->string('motor_boy_phone_no')->nullable();
            $table->string('transporter_name')->nullable();
            $table->string('transporter_phone_no')->nullable();
            $table->string('destination_state');
            $table->string('destination_city');
            $table->date('at_loading_bay');
            $table->date('gated_out')->nullable();
            $table->date('payment_disbursed');
            $table->date('valid_until')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone_no')->nullable();
            $table->string('waybill_no')->nullable();
            $table->string('loaded_weight')->nullable();
            $table->float('finance_cost', 15, 2);
            $table->float('finance_income', 15, 2);
            $table->float('net_income', 15, 2);
            $table->float('percentage_rate', 8, 2);
            $table->float('overdue_charge', 15, 2);
            $table->boolean('payment_status')->default(FALSE);
            $table->date('date_paid')->nullable();
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
        Schema::dropIfExists('tbl_kaya_pay_payment_breakdowns');
    }
}
