<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\loadingSite;
use App\transporter;
use App\truckType;
use App\trucks;
use App\drivers;
use App\product;
use App\clientFareRate;
use App\trip;
use App\tripEvent;
use App\tripWaybill;
use App\tripWaybillStatus;
use App\client;
use App\Transloader;

class sortController extends Controller
{
    public function orderLoadingSites(Request $request) {
        $loadingSites = DB::SELECT(
            DB::RAW(
                'SELECT id, loading_site FROM tbl_kaya_loading_sites WHERE id IN(SELECT loading_site_id FROM tbl_kaya_client_loading_sites WHERE client_id = "'.$request->client_id.'")'
            )
        );
        $answer = '    
        <select id="clientLoadingSite" class="filterStyle">
            <option value="">Loading Site</option>';
            foreach($loadingSites as $loadingsite) {
                $answer.='<option value="'.$loadingsite->id.'">'.$loadingsite->loading_site.'</option>';
            }
        $answer.='</select>';
        return $answer;
    }

    public function clientAll(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('DATE(a.gated_out) BETWEEN "'.$payloader->client_date_from.'" AND "'.$payloader->client_date_to.'" AND a.client_id = "'.$payloader->client_id.'" AND a.loading_site_id = "'.$payloader->loading_site_id.'" AND tracker = "'.$payloader->trip_status.'"', TRUE);

        return $this->responseLogger($trips);
    }

    public function dateRangeClientAndStatus(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('DATE(a.gated_out) BETWEEN "'.$payloader->client_date_from.'" AND "'.$payloader->client_date_to.'" AND a.client_id = "'.$payloader->client_id.'" AND a.loading_site_id = "'.$payloader->loading_site_id.'"', TRUE);
        
        return $this->responseLogger($trips);
    }

    public function dateRangeAndClient(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('DATE(a.gated_out) BETWEEN "'.$payloader->client_date_from.'" AND "'.$payloader->client_date_to.'" AND a.client_id = "'.$payloader->client_id.'"', TRUE);

        return $this->responseLogger($trips);

    }

    public function clientLoadingSiteAndTripStatus(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('a.client_id = "'.$payloader->client_id.'" AND a.loading_site_id = "'.$payloader->loading_site_id.'" AND tracker = "'.$payloader->trip_status.'"', TRUE);

        return $this->responseLogger($trips);

    }

    public function clientAndStatus(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('a.client_id = "'.$payloader->client_id.'" AND tracker = "'.$payloader->trip_status.'"', TRUE);
        
        return $this->responseLogger($trips);

    }

    public function dateRangeClientStatus(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('DATE(a.gated_out) BETWEEN "'.$payloader->client_date_from.'" AND "'.$payloader->client_date_to.'" AND a.client_id = "'.$payloader->client_id.'" AND tracker = "'.$payloader->trip_status.'"', TRUE);

        return $this->responseLogger($trips);
    }

    public function transporterOnly(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('a.transporter_id = "'.$payloader->transporter_id.'"', TRUE);
        return $this->responseLogger($trips);
    }

    public function transporterAndDateRange(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('a.transporter_id = "'.$payloader->transporter_id.'" AND DATE(a.gated_out) BETWEEN "'.$payloader->transporter_date_from.'" AND "'.$payloader->transporter_date_to.'"', TRUE);
        return $this->responseLogger($trips);
    }

    public function transporterAll(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('a.transporter_id = "'.$payloader->transporter_id.'" AND DATE(a.gated_out) BETWEEN "'.$payloader->transporter_date_from.'" AND "'.$payloader->transporter_date_to.'" AND tracker="'.$payloader->trip_status.'"', TRUE);
        return $this->responseLogger($trips);
    }

    public function transporterAndTripStatus(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('a.transporter_id = "'.$payloader->transporter_id.'" AND tracker="'.$payloader->trip_status.'"', TRUE);
        return $this->responseLogger($trips);
    }

    public function voidedTrips(Request $request) {
        $trips = $this->tripQuery('a.trip_id != "" ', FALSE);
        return $this->responseLogger($trips);
    }

    public function tripsTripStatus(Request $request) {
        $payloader = json_decode($request->payload);
        $trips = $this->tripQuery('tracker = "'.$payloader->tracker.'"', TRUE);
        return $this->responseLogger($trips);
    }

    

    function tripQuery($clause, $tripStatus) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.truck_no, d.truck_type, d.tonnage, e.transporter_name, e.phone_no, f.driver_first_name, f.driver_last_name, f.driver_phone_number, f.motor_boy_first_name, f.motor_boy_last_name, f.motor_boy_phone_no FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_trucks c JOIN tbl_kaya_truck_types d  JOIN tbl_kaya_transporters e JOIN tbl_kaya_drivers f ON a.loading_site_id = b.id AND c.id = a.truck_id AND c.truck_type_id = d.id AND a.transporter_id = e.id AND f.id = a.driver_id WHERE '.$clause.' AND trip_status = "'.$tripStatus.'" ORDER BY a.trip_id DESC  '
            )
        );
        return $query;
    }

    function responseLogger($trips) {
        $response = '
        <table class="table table-bordered">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee;">
                <tr class="font-weigth-semibold">
                    <th class="headcol">TRIP ID</th>
                    <th>LOADING SITE</td>
                    <th>WAYBILL INFO</th>
                    <th class="text-center">TRUCK INFO</th>
                    <th>TRANSPORTER</th>
                    <th>DRIVER</th>
                    <th>MOTOR BOY</th>
                    <th>CUSTOMER</th>
                </tr>
            </thead>
            <tbody id="masterDataTable">';
                $counter = 0;
                if(count($trips)) {
                    foreach($trips as $key => $trip) {
                    $counter++;
                    $counter % 2 == 0 ? $css = ' font-weight-semibold ' : $css = 'order-table font-weight-semibold';
                    if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                    if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                    if($trip->tracker == 3){ $current_stage = 'LOADING';}
                    if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                    if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                    if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                    if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                    if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}

                    $transloaderTrip = DB::SELECT(
                        DB::RAW(
                            'SELECT a.trip_id, a.reason_for_transloading, a.created_at, b.truck_no, c.driver_first_name, c.driver_phone_number, d.truck_type, d.tonnage FROM tbl_kaya_trip_transloaders a JOIN tbl_kaya_trucks b JOIN tbl_kaya_drivers c JOIN tbl_kaya_truck_types d ON a.transloaded_truck_id = b.id AND a.transloaded_driver_id = c.id AND b.truck_type_id = d.id WHERE a.trip_id = "'.$trip->id.'" '
                        )
                    );
                                          
                    $response.='
                    <tr class="'.$css.' hover" style="font-size:10px;">
                        <td>
                            <a href="trips/'.$trip->id.'/edit" class="list-icons-item text-primary-600" title="Update this trip">'.$trip->trip_id.'</a>

                            <a href="way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check text-warning-600"></i>
                            </a>
                            
                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-eye8 text-info-600"></i>
                            </a>

                            <span class="list-icons-item">';
                                if($trip->tracker < 5) {
                                    $response.='<i class="icon icon-x text-danger voidTrip" value="'.$trip->trip_id.'" title="Cancel Trip" id="'.$trip->id.'"></i>';
                                }
                                else {
                                    $response.='
                                    <i class="icon icon-checkmark2 voidTrip" title="Gated Out" value="'.$trip->trip_id.'" title="Cancel Trip" id="'.$trip->id.'"></i>';
                                }
                            $response.='
                            </span>';
                            if(isset($trip->gated_out)) {
                                $response.='<span class="badge badge-danger">'.date("d-m-Y H:i:s", strtotime($trip->gated_out)).'</span>';
                            }
                        $response.='
                        </td>
                        <td>
                            '.$trip->loading_site.'<br>
                            <span class="badge badge-info">'.$current_stage.'</span>
                            <span class="badge badge-primary">'.strtoupper($trip->exact_location_id).'</span>
                        </td>';

                        $waybillsListings = tripWaybill::SELECT('id', 'trip_id', 'sales_order_no', 'invoice_no', 'photo')->WHERE('trip_id', $trip->id)->GET();
                        $waybills = [];
                        if(count($waybillsListings) > 0) {
                            [$waybills[]] = $waybillsListings;
                        }

                        $response.='<td class="text-center font-weight-semibold">';
                            foreach($waybills as $waybill) {
                                $response.= '<a href="assets/img/waybills/'.$waybill->photo.'" target="_blank" title="View waybill '.$waybill->sales_order_no.'">'.$waybill->sales_order_no.'</a><br>';
                            }
                        
                        $response.='</td>';
                        $response.='<td>'.strtoupper($trip->truck_no).' ';
                        if(count($transloaderTrip) && $transloaderTrip[0]->trip_id == $trip->id) {
                            $response.='
                                <i class="icon-toggle ml-3"></i>';
                        }
                        $response.='<br>
                            <span class="badge badge-primary">'.strtoupper($trip->truck_type).',  '.$trip->tonnage/1000 .' T</span></td>
                        <td>'.strtoupper($trip->transporter_name).' <span class="d-block">'.$trip->phone_no.'</span></td>
                        <td>
                            '.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'<br>
                            <span class="badge badge-info">'.$trip->driver_phone_number.'</span>
                        </td>
                        <td>
                            '.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).' <br> 
                            <span class="badge badge-info">'.$trip->motor_boy_phone_no.'</span>
                        </td>
                        <td>
                            <span style="font-size:10px; color: blue">
                            '.$trip->customer_no.'</span><br> 
                            '.strtoupper($trip->customers_name).' <br> 
                            '.$trip->customer_address.'
                            </td>
                        
                    </tr>';
                    }
                }    
                else {   
                    $response.='
                    <tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>';
                }            
            $response.='
            </tbody>
        </table>';
        //return $waybills;
        return $response;
    }
}