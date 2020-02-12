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

class monthElasticSortController extends Controller
{
    public function monthonly(Request $request) {
        return 'month only';
    }

    public function monthandclient(Request $request) {
        $current_year = date('Y');
        $month = $request->month;
        $client_id = $request->client_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.transporter_destination FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_transporter_rates h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.exact_location_id = h.id WHERE a.trip_status = \'1\' AND a.tracker >= \'5\' AND year = "'.$current_year.'" AND month = "'.$month.'" AND client_id = "'.$client_id.'" ' 
            )
        );

        $tabledata = '<table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
            <tr class="font-weigth-semibold">
                <th class="text-center">#</th>
                <th class="text-center">KAID</th>
                <th>LOADING SITE</td>
                <th>SALES ORDER NO.</th>
                <th class="text-center">TRUCK NO.</th>
                <th>ACCOUNT OFFICER</th>
                <th>TRANSPORTER\'s NAME</th>
                <th>TRANSPOTER\'s NUMBER</th>
                <th>TRUCK TYPE</th>
                <th>TONNAGE<sub>(Kg)</sub></th>
                <th>DRIVER</th>
                <th class="text-center">DRIVER\'s No.</th>
                <th>MOTOR BOY</th>
                <th class="text-center">MOTOR BOY\'s NO.</th>
                <th class="text-center">INVOICE NO.</th>
                <th>CUSTOMER\'s NAME</th>
                <th class="text-center">CUSTOMER\'s NO.</th>
                <th>CUSTOMER\'s ADDRESS</th>
                <th>DESTINATION</th>
                <th>WEIGHT</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th class="text-center">GATE IN</th>
                <th>TIME SINCE GATED IN</th>
                <th class="text-center">ARRIVAL AT LOADING BAY</th>
                <th class="text-center">TIME LOADING STARTED</th>
                <th class="text-center">TIME LOADING ENDED</th>
                <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                <th class="text-center">GATED OUT</th>
                <th>LAST KNOWN LOCATION 1</th>
                <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                <th>LAST KNOWN LOCATION 2</th>
                <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                <th>TIME ARRIVED DESTINATION</th>
                <th class="text-center">OFFLOADING DURATION</th>
                <th>DISTANCE <sub>(km)</sub></th>
                <th>CURRENT STAGE</th>
                <th>WAYBILL STATUS</th>
                <th>WAYBILL INDICATOR</th>
                <th>INVOICE STATUS</th>
                <th>WAYBILL COLLECTION DATE</th>
                <th>DATE INVOICE</th>
                <th>DATE PAID</th>
               
                <th>Action</th>
            </tr>
        </thead>';

        $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
                if($trip->gated_out != '') {
                    $now = time();
                    $gatedout = strtotime($trip->gated_out);
                    $datediff = $now - $gatedout;
                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                    if($numberofdays >=0 && $numberofdays <= 3){
                        $bgcolor = '#008000';
                        $textdescription = 'HEALTHY';
                        $color = '#fff';
                    }
                    elseif($numberofdays >=4 && $numberofdays <= 7){
                        $bgcolor = '#FFBF00';
                        $textdescription = 'WARNING';
                        $color = '#fff';
                    }
                    else{
                        $bgcolor = '#FF0000';
                        $textdescription = 'EXTREME';
                        $color = '#fff';
                    }
                }
                else{
                    $bgcolor = '';
                    $textdescription = 'Not gated out yet';
                    $color= '#000';
                }
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.' hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td class="text-center">'.$trip->trip_id.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>
                    <td>'.strtoupper($trip->account_officer).'</td>
                    <td>'.strtoupper($trip->transporter_name).'</td>
                    <td class="text-center">'.$trip->phone_no.'</td>
                    <td>'.strtoupper($trip->truck_type).'</td>
                    <td>'.$trip->tonnage.'</td>
                    <td>'.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'</td>
                    <td class="text-center">'.$trip->driver_phone_number.'</td>
                    <td>'.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).'</td>
                    <td class="text-center">'.$trip->motor_boy_phone_no.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $invoiceNo){
                            if($trip->id == $invoiceNo->trip_id){
                                 $tabledata.=$invoiceNo->invoice_no.'<br>';
                            }
                        }
                        
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.$trip->customer_no.'</td>
                    <td>'.$trip->customer_address.'</td>
                    <td>'.strtoupper($trip->transporter_destination).'</td>
                    <td>'.$trip->loaded_weight.'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.$trip->loaded_quantity.'</td>
                    <td class="text-center">'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td></td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$lst.'</td>
                    <td class="text-center">'.$let.'</td>
                    <td class="text-center">'.$ddt.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td></td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">
                        '.$this->eventdetails($tripEvents, $trip, 'offload_start_time').' -
                        '.$this->eventdetails($tripEvents, $trip, 'offload_end_time').'
                    </td>
                    <td></td>
                    <td class="font-weight-semibold">'.$current_stage.'</td>
                    <td>';
                        foreach($waybillstatuses as $waybillstatus){
                            if($waybillstatus->trip_id == $trip->id){
                                $tabledata.=$waybillstatus->comment;
                            }
                        }
                    $tabledata.='</td>
                    <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>                        
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <div class="list-icons">
                                                        
                            <a href="waybill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check"></i>
                            </a>

                            <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52"></i>
                            </a>

                            <a href="#" class="list-icons-item text-danger-600" title="Cancel Trip">
                                <i class="icon-trash"></i>
                            </a>

                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                <i class="icon-file-eye"></i>
                            </a>

                        </div>
                    </td>
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No trip matches your search</td>
                </tr>';
            }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;        

    }

    public function monthandloadingsite(Request $request) {
        $current_year = date('Y');
        $month = $request->month;
        $loading_site_id = $request->loading_site_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.transporter_destination FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_transporter_rates h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.exact_location_id = h.id WHERE a.trip_status = \'1\' AND a.tracker >= \'5\' AND year = "'.$current_year.'" AND month = "'.$month.'" AND loading_site_id = "'.$loading_site_id.'" ' 
            )
        );

        $tabledata = '<table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
            <tr class="font-weigth-semibold">
                <th class="text-center">#</th>
                <th class="text-center">KAID</th>
                <th>LOADING SITE</td>
                <th>SALES ORDER NO.</th>
                <th class="text-center">TRUCK NO.</th>
                <th>ACCOUNT OFFICER</th>
                <th>TRANSPORTER\'s NAME</th>
                <th>TRANSPOTER\'s NUMBER</th>
                <th>TRUCK TYPE</th>
                <th>TONNAGE<sub>(Kg)</sub></th>
                <th>DRIVER</th>
                <th class="text-center">DRIVER\'s No.</th>
                <th>MOTOR BOY</th>
                <th class="text-center">MOTOR BOY\'s NO.</th>
                <th class="text-center">INVOICE NO.</th>
                <th>CUSTOMER\'s NAME</th>
                <th class="text-center">CUSTOMER\'s NO.</th>
                <th>CUSTOMER\'s ADDRESS</th>
                <th>DESTINATION</th>
                <th>WEIGHT</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th class="text-center">GATE IN</th>
                <th>TIME SINCE GATED IN</th>
                <th class="text-center">ARRIVAL AT LOADING BAY</th>
                <th class="text-center">TIME LOADING STARTED</th>
                <th class="text-center">TIME LOADING ENDED</th>
                <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                <th class="text-center">GATED OUT</th>
                <th>LAST KNOWN LOCATION 1</th>
                <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                <th>LAST KNOWN LOCATION 2</th>
                <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                <th>TIME ARRIVED DESTINATION</th>
                <th class="text-center">OFFLOADING DURATION</th>
                <th>DISTANCE <sub>(km)</sub></th>
                <th>CURRENT STAGE</th>
                <th>WAYBILL STATUS</th>
                <th>WAYBILL INDICATOR</th>
                <th>INVOICE STATUS</th>
                <th>WAYBILL COLLECTION DATE</th>
                <th>DATE INVOICE</th>
                <th>DATE PAID</th>
               
                <th>Action</th>
            </tr>
        </thead>';

        $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
                if($trip->gated_out != '') {
                    $now = time();
                    $gatedout = strtotime($trip->gated_out);
                    $datediff = $now - $gatedout;
                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                    if($numberofdays >=0 && $numberofdays <= 3){
                        $bgcolor = '#008000';
                        $textdescription = 'HEALTHY';
                        $color = '#fff';
                    }
                    elseif($numberofdays >=4 && $numberofdays <= 7){
                        $bgcolor = '#FFBF00';
                        $textdescription = 'WARNING';
                        $color = '#fff';
                    }
                    else{
                        $bgcolor = '#FF0000';
                        $textdescription = 'EXTREME';
                        $color = '#fff';
                    }
                }
                else{
                    $bgcolor = '';
                    $textdescription = 'Not gated out yet';
                    $color= '#000';
                }
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.' hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td class="text-center">'.$trip->trip_id.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>
                    <td>'.strtoupper($trip->account_officer).'</td>
                    <td>'.strtoupper($trip->transporter_name).'</td>
                    <td class="text-center">'.$trip->phone_no.'</td>
                    <td>'.strtoupper($trip->truck_type).'</td>
                    <td>'.$trip->tonnage.'</td>
                    <td>'.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'</td>
                    <td class="text-center">'.$trip->driver_phone_number.'</td>
                    <td>'.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).'</td>
                    <td class="text-center">'.$trip->motor_boy_phone_no.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $invoiceNo){
                            if($trip->id == $invoiceNo->trip_id){
                                 $tabledata.=$invoiceNo->invoice_no.'<br>';
                            }
                        }
                        
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.$trip->customer_no.'</td>
                    <td>'.$trip->customer_address.'</td>
                    <td>'.strtoupper($trip->transporter_destination).'</td>
                    <td>'.$trip->loaded_weight.'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.$trip->loaded_quantity.'</td>
                    <td class="text-center">'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td></td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$lst.'</td>
                    <td class="text-center">'.$let.'</td>
                    <td class="text-center">'.$ddt.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td></td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">
                        '.$this->eventdetails($tripEvents, $trip, 'offload_start_time').' -
                        '.$this->eventdetails($tripEvents, $trip, 'offload_end_time').'
                    </td>
                    <td></td>
                    <td class="font-weight-semibold">'.$current_stage.'</td>
                    <td>';
                        foreach($waybillstatuses as $waybillstatus){
                            if($waybillstatus->trip_id == $trip->id){
                                $tabledata.=$waybillstatus->comment;
                            }
                        }
                    $tabledata.='</td>
                    <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>                        
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <div class="list-icons">
                                                        
                            <a href="waybill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check"></i>
                            </a>

                            <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52"></i>
                            </a>

                            <a href="#" class="list-icons-item text-danger-600" title="Cancel Trip">
                                <i class="icon-trash"></i>
                            </a>

                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                <i class="icon-file-eye"></i>
                            </a>

                        </div>
                    </td>
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No trip matches your search</td>
                </tr>';
            }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;        
    }

    public function monthandtransporters(Request $request) {
        $current_year = date('Y');
        $month = $request->month;
        $transporter_id = $request->transporter_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.transporter_destination FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_transporter_rates h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.exact_location_id = h.id WHERE a.trip_status = \'1\' AND a.tracker >= \'5\' AND year = "'.$current_year.'" AND month = "'.$month.'" AND a.transporter_id = "'.$transporter_id.'" ' 
            )
        );

        $tabledata = '<table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
            <tr class="font-weigth-semibold">
                <th class="text-center">#</th>
                <th class="text-center">KAID</th>
                <th>LOADING SITE</td>
                <th>SALES ORDER NO.</th>
                <th class="text-center">TRUCK NO.</th>
                <th>ACCOUNT OFFICER</th>
                <th>TRANSPORTER\'s NAME</th>
                <th>TRANSPOTER\'s NUMBER</th>
                <th>TRUCK TYPE</th>
                <th>TONNAGE<sub>(Kg)</sub></th>
                <th>DRIVER</th>
                <th class="text-center">DRIVER\'s No.</th>
                <th>MOTOR BOY</th>
                <th class="text-center">MOTOR BOY\'s NO.</th>
                <th class="text-center">INVOICE NO.</th>
                <th>CUSTOMER\'s NAME</th>
                <th class="text-center">CUSTOMER\'s NO.</th>
                <th>CUSTOMER\'s ADDRESS</th>
                <th>DESTINATION</th>
                <th>WEIGHT</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th class="text-center">GATE IN</th>
                <th>TIME SINCE GATED IN</th>
                <th class="text-center">ARRIVAL AT LOADING BAY</th>
                <th class="text-center">TIME LOADING STARTED</th>
                <th class="text-center">TIME LOADING ENDED</th>
                <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                <th class="text-center">GATED OUT</th>
                <th>LAST KNOWN LOCATION 1</th>
                <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                <th>LAST KNOWN LOCATION 2</th>
                <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                <th>TIME ARRIVED DESTINATION</th>
                <th class="text-center">OFFLOADING DURATION</th>
                <th>DISTANCE <sub>(km)</sub></th>
                <th>CURRENT STAGE</th>
                <th>WAYBILL STATUS</th>
                <th>WAYBILL INDICATOR</th>
                <th>INVOICE STATUS</th>
                <th>WAYBILL COLLECTION DATE</th>
                <th>DATE INVOICE</th>
                <th>DATE PAID</th>
               
                <th>Action</th>
            </tr>
        </thead>';

        $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
                if($trip->gated_out != '') {
                    $now = time();
                    $gatedout = strtotime($trip->gated_out);
                    $datediff = $now - $gatedout;
                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                    if($numberofdays >=0 && $numberofdays <= 3){
                        $bgcolor = '#008000';
                        $textdescription = 'HEALTHY';
                        $color = '#fff';
                    }
                    elseif($numberofdays >=4 && $numberofdays <= 7){
                        $bgcolor = '#FFBF00';
                        $textdescription = 'WARNING';
                        $color = '#fff';
                    }
                    else{
                        $bgcolor = '#FF0000';
                        $textdescription = 'EXTREME';
                        $color = '#fff';
                    }
                }
                else{
                    $bgcolor = '';
                    $textdescription = 'Not gated out yet';
                    $color= '#000';
                }
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.' hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td class="text-center">'.$trip->trip_id.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>
                    <td>'.strtoupper($trip->account_officer).'</td>
                    <td>'.strtoupper($trip->transporter_name).'</td>
                    <td class="text-center">'.$trip->phone_no.'</td>
                    <td>'.strtoupper($trip->truck_type).'</td>
                    <td>'.$trip->tonnage.'</td>
                    <td>'.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'</td>
                    <td class="text-center">'.$trip->driver_phone_number.'</td>
                    <td>'.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).'</td>
                    <td class="text-center">'.$trip->motor_boy_phone_no.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $invoiceNo){
                            if($trip->id == $invoiceNo->trip_id){
                                 $tabledata.=$invoiceNo->invoice_no.'<br>';
                            }
                        }
                        
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.$trip->customer_no.'</td>
                    <td>'.$trip->customer_address.'</td>
                    <td>'.strtoupper($trip->transporter_destination).'</td>
                    <td>'.$trip->loaded_weight.'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.$trip->loaded_quantity.'</td>
                    <td class="text-center">'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td></td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$lst.'</td>
                    <td class="text-center">'.$let.'</td>
                    <td class="text-center">'.$ddt.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td></td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">
                        '.$this->eventdetails($tripEvents, $trip, 'offload_start_time').' -
                        '.$this->eventdetails($tripEvents, $trip, 'offload_end_time').'
                    </td>
                    <td></td>
                    <td class="font-weight-semibold">'.$current_stage.'</td>
                    <td>';
                        foreach($waybillstatuses as $waybillstatus){
                            if($waybillstatus->trip_id == $trip->id){
                                $tabledata.=$waybillstatus->comment;
                            }
                        }
                    $tabledata.='</td>
                    <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>                        
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <div class="list-icons">
                                                        
                            <a href="waybill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check"></i>
                            </a>

                            <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52"></i>
                            </a>

                            <a href="#" class="list-icons-item text-danger-600" title="Cancel Trip">
                                <i class="icon-trash"></i>
                            </a>

                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                <i class="icon-file-eye"></i>
                            </a>

                        </div>
                    </td>
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No trip matches your search</td>
                </tr>';
            }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;        
    }

    public function monthandproducts(Request $request) {
        $current_year = date('Y');
        $month = $request->month;
        $product_id = $request->product_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.transporter_destination FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_transporter_rates h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.exact_location_id = h.id WHERE a.trip_status = \'1\' AND a.tracker >= \'5\' AND year = "'.$current_year.'" AND month = "'.$month.'" AND product_id = "'.$product_id.'" ' 
            )
        );

        $tabledata = '<table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
            <tr class="font-weigth-semibold">
                <th class="text-center">#</th>
                <th class="text-center">KAID</th>
                <th>LOADING SITE</td>
                <th>SALES ORDER NO.</th>
                <th class="text-center">TRUCK NO.</th>
                <th>ACCOUNT OFFICER</th>
                <th>TRANSPORTER\'s NAME</th>
                <th>TRANSPOTER\'s NUMBER</th>
                <th>TRUCK TYPE</th>
                <th>TONNAGE<sub>(Kg)</sub></th>
                <th>DRIVER</th>
                <th class="text-center">DRIVER\'s No.</th>
                <th>MOTOR BOY</th>
                <th class="text-center">MOTOR BOY\'s NO.</th>
                <th class="text-center">INVOICE NO.</th>
                <th>CUSTOMER\'s NAME</th>
                <th class="text-center">CUSTOMER\'s NO.</th>
                <th>CUSTOMER\'s ADDRESS</th>
                <th>DESTINATION</th>
                <th>WEIGHT</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th class="text-center">GATE IN</th>
                <th>TIME SINCE GATED IN</th>
                <th class="text-center">ARRIVAL AT LOADING BAY</th>
                <th class="text-center">TIME LOADING STARTED</th>
                <th class="text-center">TIME LOADING ENDED</th>
                <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                <th class="text-center">GATED OUT</th>
                <th>LAST KNOWN LOCATION 1</th>
                <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                <th>LAST KNOWN LOCATION 2</th>
                <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                <th>TIME ARRIVED DESTINATION</th>
                <th class="text-center">OFFLOADING DURATION</th>
                <th>DISTANCE <sub>(km)</sub></th>
                <th>CURRENT STAGE</th>
                <th>WAYBILL STATUS</th>
                <th>WAYBILL INDICATOR</th>
                <th>INVOICE STATUS</th>
                <th>WAYBILL COLLECTION DATE</th>
                <th>DATE INVOICE</th>
                <th>DATE PAID</th>
               
                <th>Action</th>
            </tr>
        </thead>';

        $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
                if($trip->gated_out != '') {
                    $now = time();
                    $gatedout = strtotime($trip->gated_out);
                    $datediff = $now - $gatedout;
                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                    if($numberofdays >=0 && $numberofdays <= 3){
                        $bgcolor = '#008000';
                        $textdescription = 'HEALTHY';
                        $color = '#fff';
                    }
                    elseif($numberofdays >=4 && $numberofdays <= 7){
                        $bgcolor = '#FFBF00';
                        $textdescription = 'WARNING';
                        $color = '#fff';
                    }
                    else{
                        $bgcolor = '#FF0000';
                        $textdescription = 'EXTREME';
                        $color = '#fff';
                    }
                }
                else{
                    $bgcolor = '';
                    $textdescription = 'Not gated out yet';
                    $color= '#000';
                }
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.' hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td class="text-center">'.$trip->trip_id.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>
                    <td>'.strtoupper($trip->account_officer).'</td>
                    <td>'.strtoupper($trip->transporter_name).'</td>
                    <td class="text-center">'.$trip->phone_no.'</td>
                    <td>'.strtoupper($trip->truck_type).'</td>
                    <td>'.$trip->tonnage.'</td>
                    <td>'.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'</td>
                    <td class="text-center">'.$trip->driver_phone_number.'</td>
                    <td>'.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).'</td>
                    <td class="text-center">'.$trip->motor_boy_phone_no.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $invoiceNo){
                            if($trip->id == $invoiceNo->trip_id){
                                 $tabledata.=$invoiceNo->invoice_no.'<br>';
                            }
                        }
                        
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.$trip->customer_no.'</td>
                    <td>'.$trip->customer_address.'</td>
                    <td>'.strtoupper($trip->transporter_destination).'</td>
                    <td>'.$trip->loaded_weight.'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.$trip->loaded_quantity.'</td>
                    <td class="text-center">'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td></td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$lst.'</td>
                    <td class="text-center">'.$let.'</td>
                    <td class="text-center">'.$ddt.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td></td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">
                        '.$this->eventdetails($tripEvents, $trip, 'offload_start_time').' -
                        '.$this->eventdetails($tripEvents, $trip, 'offload_end_time').'
                    </td>
                    <td></td>
                    <td class="font-weight-semibold">'.$current_stage.'</td>
                    <td>';
                        foreach($waybillstatuses as $waybillstatus){
                            if($waybillstatus->trip_id == $trip->id){
                                $tabledata.=$waybillstatus->comment;
                            }
                        }
                    $tabledata.='</td>
                    <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>                        
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <div class="list-icons">
                                                        
                            <a href="waybill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check"></i>
                            </a>

                            <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52"></i>
                            </a>

                            <a href="#" class="list-icons-item text-danger-600" title="Cancel Trip">
                                <i class="icon-trash"></i>
                            </a>

                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                <i class="icon-file-eye"></i>
                            </a>

                        </div>
                    </td>
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No trip matches your search</td>
                </tr>';
            }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;        
    }

    public function monthanddestination(Request $request) {
        $current_year = date('Y');
        $month = $request->month;
        $state_id = $request->state_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.transporter_destination FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_transporter_rates h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.exact_location_id = h.id WHERE a.trip_status = \'1\' AND a.tracker >= \'5\' AND year = "'.$current_year.'" AND month = "'.$month.'" AND destination_state_id = "'.$state_id.'" ' 
            )
        );

        $tabledata = '<table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
            <tr class="font-weigth-semibold">
                <th class="text-center">#</th>
                <th class="text-center">KAID</th>
                <th>LOADING SITE</td>
                <th>SALES ORDER NO.</th>
                <th class="text-center">TRUCK NO.</th>
                <th>ACCOUNT OFFICER</th>
                <th>TRANSPORTER\'s NAME</th>
                <th>TRANSPOTER\'s NUMBER</th>
                <th>TRUCK TYPE</th>
                <th>TONNAGE<sub>(Kg)</sub></th>
                <th>DRIVER</th>
                <th class="text-center">DRIVER\'s No.</th>
                <th>MOTOR BOY</th>
                <th class="text-center">MOTOR BOY\'s NO.</th>
                <th class="text-center">INVOICE NO.</th>
                <th>CUSTOMER\'s NAME</th>
                <th class="text-center">CUSTOMER\'s NO.</th>
                <th>CUSTOMER\'s ADDRESS</th>
                <th>DESTINATION</th>
                <th>WEIGHT</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th class="text-center">GATE IN</th>
                <th>TIME SINCE GATED IN</th>
                <th class="text-center">ARRIVAL AT LOADING BAY</th>
                <th class="text-center">TIME LOADING STARTED</th>
                <th class="text-center">TIME LOADING ENDED</th>
                <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                <th class="text-center">GATED OUT</th>
                <th>LAST KNOWN LOCATION 1</th>
                <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                <th>LAST KNOWN LOCATION 2</th>
                <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                <th>TIME ARRIVED DESTINATION</th>
                <th class="text-center">OFFLOADING DURATION</th>
                <th>DISTANCE <sub>(km)</sub></th>
                <th>CURRENT STAGE</th>
                <th>WAYBILL STATUS</th>
                <th>WAYBILL INDICATOR</th>
                <th>INVOICE STATUS</th>
                <th>WAYBILL COLLECTION DATE</th>
                <th>DATE INVOICE</th>
                <th>DATE PAID</th>
               
                <th>Action</th>
            </tr>
        </thead>';

        $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
                if($trip->gated_out != '') {
                    $now = time();
                    $gatedout = strtotime($trip->gated_out);
                    $datediff = $now - $gatedout;
                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                    if($numberofdays >=0 && $numberofdays <= 3){
                        $bgcolor = '#008000';
                        $textdescription = 'HEALTHY';
                        $color = '#fff';
                    }
                    elseif($numberofdays >=4 && $numberofdays <= 7){
                        $bgcolor = '#FFBF00';
                        $textdescription = 'WARNING';
                        $color = '#fff';
                    }
                    else{
                        $bgcolor = '#FF0000';
                        $textdescription = 'EXTREME';
                        $color = '#fff';
                    }
                }
                else{
                    $bgcolor = '';
                    $textdescription = 'Not gated out yet';
                    $color= '#000';
                }
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.' hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td class="text-center">'.$trip->trip_id.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>
                    <td>'.strtoupper($trip->account_officer).'</td>
                    <td>'.strtoupper($trip->transporter_name).'</td>
                    <td class="text-center">'.$trip->phone_no.'</td>
                    <td>'.strtoupper($trip->truck_type).'</td>
                    <td>'.$trip->tonnage.'</td>
                    <td>'.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'</td>
                    <td class="text-center">'.$trip->driver_phone_number.'</td>
                    <td>'.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).'</td>
                    <td class="text-center">'.$trip->motor_boy_phone_no.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $invoiceNo){
                            if($trip->id == $invoiceNo->trip_id){
                                 $tabledata.=$invoiceNo->invoice_no.'<br>';
                            }
                        }
                        
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.$trip->customer_no.'</td>
                    <td>'.$trip->customer_address.'</td>
                    <td>'.strtoupper($trip->transporter_destination).'</td>
                    <td>'.$trip->loaded_weight.'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.$trip->loaded_quantity.'</td>
                    <td class="text-center">'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td></td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$lst.'</td>
                    <td class="text-center">'.$let.'</td>
                    <td class="text-center">'.$ddt.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td></td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">
                        '.$this->eventdetails($tripEvents, $trip, 'offload_start_time').' -
                        '.$this->eventdetails($tripEvents, $trip, 'offload_end_time').'
                    </td>
                    <td></td>
                    <td class="font-weight-semibold">'.$current_stage.'</td>
                    <td>';
                        foreach($waybillstatuses as $waybillstatus){
                            if($waybillstatus->trip_id == $trip->id){
                                $tabledata.=$waybillstatus->comment;
                            }
                        }
                    $tabledata.='</td>
                    <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>                        
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <div class="list-icons">
                                                        
                            <a href="waybill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check"></i>
                            </a>

                            <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52"></i>
                            </a>

                            <a href="#" class="list-icons-item text-danger-600" title="Cancel Trip">
                                <i class="icon-trash"></i>
                            </a>

                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                <i class="icon-file-eye"></i>
                            </a>

                        </div>
                    </td>
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No trip matches your search</td>
                </tr>';
            }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;        
    }

    public function monthclientloadingsite(Request $request) {
        $current_year = date('Y');
        $month = $request->month;
        $client_id = $request->client_id;
        $loading_site_id = $request->loading_site_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.transporter_destination FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_transporter_rates h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.exact_location_id = h.id WHERE a.trip_status = \'1\' AND a.tracker >= \'5\' AND year = "'.$current_year.'" AND month = "'.$month.'" AND client_id = "'.$client_id.'" AND loading_site_id = "'.$loading_site_id.'" ' 
            )
        );

        $tabledata = '<table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
            <tr class="font-weigth-semibold">
                <th class="text-center">#</th>
                <th class="text-center">KAID</th>
                <th>LOADING SITE</td>
                <th>SALES ORDER NO.</th>
                <th class="text-center">TRUCK NO.</th>
                <th>ACCOUNT OFFICER</th>
                <th>TRANSPORTER\'s NAME</th>
                <th>TRANSPOTER\'s NUMBER</th>
                <th>TRUCK TYPE</th>
                <th>TONNAGE<sub>(Kg)</sub></th>
                <th>DRIVER</th>
                <th class="text-center">DRIVER\'s No.</th>
                <th>MOTOR BOY</th>
                <th class="text-center">MOTOR BOY\'s NO.</th>
                <th class="text-center">INVOICE NO.</th>
                <th>CUSTOMER\'s NAME</th>
                <th class="text-center">CUSTOMER\'s NO.</th>
                <th>CUSTOMER\'s ADDRESS</th>
                <th>DESTINATION</th>
                <th>WEIGHT</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th class="text-center">GATE IN</th>
                <th>TIME SINCE GATED IN</th>
                <th class="text-center">ARRIVAL AT LOADING BAY</th>
                <th class="text-center">TIME LOADING STARTED</th>
                <th class="text-center">TIME LOADING ENDED</th>
                <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                <th class="text-center">GATED OUT</th>
                <th>LAST KNOWN LOCATION 1</th>
                <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                <th>LAST KNOWN LOCATION 2</th>
                <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                <th>TIME ARRIVED DESTINATION</th>
                <th class="text-center">OFFLOADING DURATION</th>
                <th>DISTANCE <sub>(km)</sub></th>
                <th>CURRENT STAGE</th>
                <th>WAYBILL STATUS</th>
                <th>WAYBILL INDICATOR</th>
                <th>INVOICE STATUS</th>
                <th>WAYBILL COLLECTION DATE</th>
                <th>DATE INVOICE</th>
                <th>DATE PAID</th>
               
                <th>Action</th>
            </tr>
        </thead>';

        $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
                if($trip->gated_out != '') {
                    $now = time();
                    $gatedout = strtotime($trip->gated_out);
                    $datediff = $now - $gatedout;
                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                    if($numberofdays >=0 && $numberofdays <= 3){
                        $bgcolor = '#008000';
                        $textdescription = 'HEALTHY';
                        $color = '#fff';
                    }
                    elseif($numberofdays >=4 && $numberofdays <= 7){
                        $bgcolor = '#FFBF00';
                        $textdescription = 'WARNING';
                        $color = '#fff';
                    }
                    else{
                        $bgcolor = '#FF0000';
                        $textdescription = 'EXTREME';
                        $color = '#fff';
                    }
                }
                else{
                    $bgcolor = '';
                    $textdescription = 'Not gated out yet';
                    $color= '#000';
                }
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.' hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td class="text-center">'.$trip->trip_id.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>
                    <td>'.strtoupper($trip->account_officer).'</td>
                    <td>'.strtoupper($trip->transporter_name).'</td>
                    <td class="text-center">'.$trip->phone_no.'</td>
                    <td>'.strtoupper($trip->truck_type).'</td>
                    <td>'.$trip->tonnage.'</td>
                    <td>'.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'</td>
                    <td class="text-center">'.$trip->driver_phone_number.'</td>
                    <td>'.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).'</td>
                    <td class="text-center">'.$trip->motor_boy_phone_no.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $invoiceNo){
                            if($trip->id == $invoiceNo->trip_id){
                                 $tabledata.=$invoiceNo->invoice_no.'<br>';
                            }
                        }
                        
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.$trip->customer_no.'</td>
                    <td>'.$trip->customer_address.'</td>
                    <td>'.strtoupper($trip->transporter_destination).'</td>
                    <td>'.$trip->loaded_weight.'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.$trip->loaded_quantity.'</td>
                    <td class="text-center">'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td></td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$lst.'</td>
                    <td class="text-center">'.$let.'</td>
                    <td class="text-center">'.$ddt.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td></td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">
                        '.$this->eventdetails($tripEvents, $trip, 'offload_start_time').' -
                        '.$this->eventdetails($tripEvents, $trip, 'offload_end_time').'
                    </td>
                    <td></td>
                    <td class="font-weight-semibold">'.$current_stage.'</td>
                    <td>';
                        foreach($waybillstatuses as $waybillstatus){
                            if($waybillstatus->trip_id == $trip->id){
                                $tabledata.=$waybillstatus->comment;
                            }
                        }
                    $tabledata.='</td>
                    <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>                        
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <div class="list-icons">
                                                        
                            <a href="waybill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check"></i>
                            </a>

                            <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52"></i>
                            </a>

                            <a href="#" class="list-icons-item text-danger-600" title="Cancel Trip">
                                <i class="icon-trash"></i>
                            </a>

                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                <i class="icon-file-eye"></i>
                            </a>

                        </div>
                    </td>
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No trip matches your search</td>
                </tr>';
            }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;        
    }

    public function monthdestinationexact(Request $request) {
        $current_year = date('Y');
        $month = $request->month;
        $state_id = $request->state_id;
        $exact_location_id = $request->exact_location_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();


        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.transporter_destination FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN tbl_kaya_transporter_rates h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.exact_location_id = h.id WHERE a.trip_status = \'1\' AND a.tracker >= \'5\' AND year = "'.$current_year.'" AND month = "'.$month.'" AND destination_state_id = "'.$state_id.'" AND exact_location_id = "'.$exact_location_id.'" ' 
            )
        );

        $tabledata = '<table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
            <tr class="font-weigth-semibold">
                <th class="text-center">#</th>
                <th class="text-center">KAID</th>
                <th>LOADING SITE</td>
                <th>SALES ORDER NO.</th>
                <th class="text-center">TRUCK NO.</th>
                <th>ACCOUNT OFFICER</th>
                <th>TRANSPORTER\'s NAME</th>
                <th>TRANSPOTER\'s NUMBER</th>
                <th>TRUCK TYPE</th>
                <th>TONNAGE<sub>(Kg)</sub></th>
                <th>DRIVER</th>
                <th class="text-center">DRIVER\'s No.</th>
                <th>MOTOR BOY</th>
                <th class="text-center">MOTOR BOY\'s NO.</th>
                <th class="text-center">INVOICE NO.</th>
                <th>CUSTOMER\'s NAME</th>
                <th class="text-center">CUSTOMER\'s NO.</th>
                <th>CUSTOMER\'s ADDRESS</th>
                <th>DESTINATION</th>
                <th>WEIGHT</th>
                <th>PRODUCT</th>
                <th>QUANTITY</th>
                <th class="text-center">GATE IN</th>
                <th>TIME SINCE GATED IN</th>
                <th class="text-center">ARRIVAL AT LOADING BAY</th>
                <th class="text-center">TIME LOADING STARTED</th>
                <th class="text-center">TIME LOADING ENDED</th>
                <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                <th class="text-center">GATED OUT</th>
                <th>LAST KNOWN LOCATION 1</th>
                <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                <th>LAST KNOWN LOCATION 2</th>
                <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                <th>TIME ARRIVED DESTINATION</th>
                <th class="text-center">OFFLOADING DURATION</th>
                <th>DISTANCE <sub>(km)</sub></th>
                <th>CURRENT STAGE</th>
                <th>WAYBILL STATUS</th>
                <th>WAYBILL INDICATOR</th>
                <th>INVOICE STATUS</th>
                <th>WAYBILL COLLECTION DATE</th>
                <th>DATE INVOICE</th>
                <th>DATE PAID</th>
               
                <th>Action</th>
            </tr>
        </thead>';

        $tabledata.='<tbody id="masterDataTable">';
            $counter = 0;
            if(count($orders)){
                foreach($orders as $trip) {
                $counter++;
                $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'order-table font-weight-semibold';
                if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                if($trip->tracker == 3){ $current_stage = 'LOADING';}
                if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                
                if($trip->gated_out != '') {
                    $now = time();
                    $gatedout = strtotime($trip->gated_out);
                    $datediff = $now - $gatedout;
                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                    if($numberofdays >=0 && $numberofdays <= 3){
                        $bgcolor = '#008000';
                        $textdescription = 'HEALTHY';
                        $color = '#fff';
                    }
                    elseif($numberofdays >=4 && $numberofdays <= 7){
                        $bgcolor = '#FFBF00';
                        $textdescription = 'WARNING';
                        $color = '#fff';
                    }
                    else{
                        $bgcolor = '#FF0000';
                        $textdescription = 'EXTREME';
                        $color = '#fff';
                    }
                }
                else{
                    $bgcolor = '';
                    $textdescription = 'Not gated out yet';
                    $color= '#000';
                }
        $trip->arrival_at_loading_bay ? $alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
        $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
        $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
        $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
        $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                $tabledata.='<tr class="'.$css.' hover" style="font-size:10px;">
                    <td>'.$counter.'</td>
                    <td class="text-center">'.$trip->trip_id.'</td>
                    <td>'.$trip->loading_site.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $salesNo){
                            if($trip->id == $salesNo->trip_id){
                            $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                            '.$salesNo->sales_order_no.'<br>
                            </a>';
                            }
                        }
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->truck_no).'</td>
                    <td>'.strtoupper($trip->account_officer).'</td>
                    <td>'.strtoupper($trip->transporter_name).'</td>
                    <td class="text-center">'.$trip->phone_no.'</td>
                    <td>'.strtoupper($trip->truck_type).'</td>
                    <td>'.$trip->tonnage.'</td>
                    <td>'.strtoupper($trip->driver_first_name).' '.strtoupper($trip->driver_last_name).'</td>
                    <td class="text-center">'.$trip->driver_phone_number.'</td>
                    <td>'.strtoupper($trip->motor_boy_first_name).' '.strtoupper($trip->motor_boy_last_name).'</td>
                    <td class="text-center">'.$trip->motor_boy_phone_no.'</td>
                    <td class="text-center font-weight-semibold">';
                        foreach($tripWaybills as $invoiceNo){
                            if($trip->id == $invoiceNo->trip_id){
                                 $tabledata.=$invoiceNo->invoice_no.'<br>';
                            }
                        }
                        
                    $tabledata.='</td>
                    <td>'.strtoupper($trip->customers_name).'</td>
                    <td class="text-center">'.$trip->customer_no.'</td>
                    <td>'.$trip->customer_address.'</td>
                    <td>'.strtoupper($trip->transporter_destination).'</td>
                    <td>'.$trip->loaded_weight.'</td>
                    <td>'.$trip->product.'</td>
                    <td>'.$trip->loaded_quantity.'</td>
                    <td class="text-center">'.date('Y-m-d, g:i A',strtotime($trip->gate_in)).'</td>
                    <td></td>
                    <td class="text-center">'.$alb.'</td>
                    <td class="text-center">'.$lst.'</td>
                    <td class="text-center">'.$let.'</td>
                    <td class="text-center">'.$ddt.'</td>
                    <td class="text-center">'.$gto.'</td>
                    
                    <td></td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_one').'</td>
                    <td>'.$this->eventdetails($tripEvents, $trip, 'location_two_comment').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'location_check_two').'</td>
                    <td class="text-center">'.$this->eventdetails($tripEvents, $trip, 'time_arrived_destination').'</td>
                    <td class="text-center">
                        '.$this->eventdetails($tripEvents, $trip, 'offload_start_time').' -
                        '.$this->eventdetails($tripEvents, $trip, 'offload_end_time').'
                    </td>
                    <td></td>
                    <td class="font-weight-semibold">'.$current_stage.'</td>
                    <td>';
                        foreach($waybillstatuses as $waybillstatus){
                            if($waybillstatus->trip_id == $trip->id){
                                $tabledata.=$waybillstatus->comment;
                            }
                        }
                    $tabledata.='</td>
                    <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>                        
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td>
                        <div class="list-icons">
                                                        
                            <a href="waybill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check"></i>
                            </a>

                            <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52"></i>
                            </a>

                            <a href="#" class="list-icons-item text-danger-600" title="Cancel Trip">
                                <i class="icon-trash"></i>
                            </a>

                            <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                <i class="icon-file-eye"></i>
                            </a>

                        </div>
                    </td>
                </tr>';
                
                }
            } else {   
                $tabledata.='<tr>
                    <td class="table-success" colspan="30">No trip matches your search</td>
                </tr>';
            }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;        
    }









    function timeDifference($gatedIn, $timeArrivedLoadingBay){
        if($gatedIn && $timeArrivedLoadingBay != '') {
            $mydate1 = new DateTime($gatedIn);
            $mydate2 = new DateTime($timeArrivedLoadingBay);
            $interval = $mydate1->diff($mydate2);
            $elapsed = $interval->format('%a days %h hours %i minutes');
        }
        else{
            $elapsed = '';
        }
        return $elapsed;
    }
    
    function eventdetails($arrayRecord, $master, $field){
        foreach($arrayRecord as $object) {
            if($master->id == $object->trip_id) {
                if(($field == 'location_check_one' && $field!='')){
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'location_check_two' && $object->$field!='')) {
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'time_arrived_destination' && $object->$field!='')) {
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'offload_start_time' && $object->$field!='')){
                    return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                elseif(($field == 'offload_end_time' && $object->$field!='')){
                     return date('Y-m-d, g:i A', strtotime($object->$field));
                }
                else{
                    return $object->$field;
                }
                break;
            }
            continue;
        }
    }

}
