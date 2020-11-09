<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\trip;
use App\tripWaybill;

class FinancialReportController extends Controller
{
    private $currentDate;

    public function financialReporting() {
        return view('finance.report.report');
    }

    public function waybillStatus(Request $request) {
        $currentDate = date('Y-m-d H:i:s');
        $type = strtoupper($request->v);
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.trip_id, truck_no, loading_site, transporter_name, invoice_status, gated_out, DATEDIFF("'.$currentDate.'", a.gated_out) as gated_out_since FROM tbl_kaya_trips a JOIN tbl_kaya_trip_waybill_statuses b JOIN tbl_kaya_trucks c JOIN tbl_kaya_loading_sites d JOIN tbl_kaya_transporters e ON a.id = b.trip_id AND a.truck_id = c.id AND a.loading_site_id = d.id AND a.transporter_id = e.id WHERE a.tracker = \'8\' AND b.invoice_status = FALSE' 
            )
        );

        return $this->responseLogger($trips, $type);
    }

    

    function responseLogger($trips, $selectedModule) {
        $waybills = [];
        foreach($trips as $tripListings) {
            $associatedWaybills = tripWaybill::SELECT('trip_id', 'sales_order_no', 'invoice_no')->WHERE('trip_id', $tripListings->id)->GET();
            if(count($associatedWaybills)) {
                [$waybills[]] = $associatedWaybills;
            }
        }
        $response ='
        <table class="table table-striped">
            <thead>
                <tr class="font-weight-bold font-size-xs font-weight-bold">
                    <th colspan="2">Total: '.count($trips).' Trips</th>
                    <th colspan="6" class="text-right text-success pointer" id="exportBtn">EXPORT <i class="icon-cloud-download2"></i></th>
                </tr>
                <tr class="font-size-xs bg-primary-400">
                    <th>TRIP ID</th>
                    <th>TRUCK NO</th>
                    <th>WAYBILL INFO</th>
                    <th class="text-center">LOADING SITE</th>
                    <th class="text-center">GATED OUT</th>
                    <th class="text-center bg-danger-400">SINCE</th>
                    <th>TRANSPORTER</th>
                </tr>
            </thead>
            <tbody class="font-size-xs">';
                if(count($trips)) {
                    foreach($trips as $trip) {
                        $response.='
                            <tr>
                                <td>'.$trip->trip_id.'</td>
                                <td>'.$trip->truck_no.'</td>';
                                $response.='
                                <td>';
                                foreach($waybills as $key=> $waybillInfo) {
                                    if($waybillInfo->trip_id === $trip->id) {
                                        $response.=$waybillInfo->sales_order_no.', '.$waybillInfo->invoice_no.'<br>';
                                    }
                                }
                                $response.='</td>
                                <td class="text-center">'.$trip->loading_site.'</td>
                                <td class="text-center">'.date('d-m-Y', strtotime($trip->gated_out)).'</td>
                                <td class="bg-danger-400 text-center">'.$trip->gated_out_since.' Days</td>
                                <td>'.$trip->transporter_name.'</td>
                            </tr>
                        ';
                    }
                }
                else {
                $response.='<tr>
                    <td colspan="8" class="bg-warning-300 text-center font-size-xs">You do not have any record for '.ucwords($selectedModule).'</td>
                </tr>';
                }
            $response.='
            </tbody>
        </table>';
        return $response;
    }
    
}
