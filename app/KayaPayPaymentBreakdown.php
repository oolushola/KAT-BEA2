<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KayaPayPaymentBreakdown extends Model
{
    protected $table = 'tbl_kaya_pay_payment_breakdowns';
    protected $fillable = [
        'kaya_pay_id',
        'client_id',
        'loading_site',
        'gated_in',
        'truck_no',
        'driver_name',
        'driver_phone_no',
        'motor_boy_name',
        'motor_boy_phone_no',
        'transporter_name',
        'transporter_phone_no',
        'destination_state',
        'destination_city',
        'at_loading_bay',
        'gated_out',
        'payment_disbursed',
        'valid_until',
        'customer_name',
        'customer_phone_no',
        'waybill_no',
        'loaded_weight',
        'finance_cost',
        'finance_income',
        'net_income',
        'percentage_rate',
        'overdue_charge',
        'payment_status',
        'date_paid'
    ];
}
