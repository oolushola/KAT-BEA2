<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KayaPayPaymentBreakdown;
use App\ClientArrangement;
use Illuminate\Support\Facades\DB;

class kayaPayController extends Controller
{
    public function dashboard() {
        $tripsDisbursedToday = KayaPayPaymentBreakdown::WHEREDATE('payment_disbursed', date('Y-m-d'))->GET();
        $tripsDueToday = $this->query('DATEDIFF(valid_until, NOW()) = 0 AND payment_status = FALSE');
        $tripsOverdueToday = $this->query('DATEDIFF(valid_until, NOW()) < 0 AND payment_status = FALSE');
        $currentMonthFinances = DB::SELECT(
            DB::RAW(
                'SELECT SUM(finance_cost) AS totalFinanceCost, SUM(finance_income) AS totalFinanceIncome,  SUM(finance_income) - SUM(finance_cost) AS totalNetIncome FROM tbl_kaya_pay_payment_breakdowns WHERE YEAR(payment_disbursed) = "'.date("Y").'" AND MONTH(payment_disbursed) = "'.date("m").'"'
            )
        );
        $financeProjections = [
            number_format($currentMonthFinances[0]->totalFinanceCost / 1000000, 3),
            number_format($currentMonthFinances[0]->totalFinanceIncome / 1000000, 3),
            number_format($currentMonthFinances[0]->totalNetIncome / 100000, 3)
        ];

        $currentDay = date('d');
        $counter = 1;
        do {
            $datesOfTheMonth = date('Y-m-').$counter;
            $amountDisbursed = KayaPayPaymentBreakdown::WHERE('payment_disbursed', $datesOfTheMonth)->GET()->COUNT();
            $disbursedFor[] = $amountDisbursed; 
            $counter++;
        } while ($counter <= $currentDay);        

        return view('kaya-pay/dashboard', 
            compact(
                'tripsDisbursedToday',
                'tripsDueToday',
                'tripsOverdueToday',
                'financeProjections',
                'disbursedFor'
            )
        );
    }

    public function query($clause) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.id, kaya_pay_id, loading_site, truck_no, payment_disbursed, destination_state, destination_city, valid_until, waybill_no, finance_income, finance_cost, net_income, percentage_rate, overdue_charge,  b.company_name FROM tbl_kaya_pay_payment_breakdowns a JOIN tbl_kaya_clients b ON a.client_id = b.id WHERE '.$clause.' '
            )
        );
        return $query;
    }
}
