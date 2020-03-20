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

class sortController extends Controller
{
    public function clienttrips(Request $request) {
        $client_id = $request->client_id;
        $orders = $this->tripQueryBuilder($client_id, 'client_id');
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();
        $invoiceCriteria = tripWaybillStatus::GET();


        $loadingSites = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_kaya_loading_sites WHERE id IN(SELECT loading_site_id FROM tbl_kaya_client_loading_sites where client_id = '.$client_id.')'
            )
        );
        $loadingbay = '<select class="form-control" id="loadingBayId">
            <option value="0">Loading Site</option>';
                foreach($loadingSites as $siteofloading){
                   $loadingbay.='<option value="'.$siteofloading->id.'">'.$siteofloading->loading_site.'</option>';
                }
        $loadingbay.='</select>';

        $tabledata = '<table class="table table-bordered">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
                <tr class="font-weigth-semibold">
                    <th class="text-center">#</th>
                    <th class="text-center">KAID</th>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO.</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th>ACCOUNT OFFICER</th>
                    <th>FIELD OPS</th>
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
                    <th>CURRENT STAGE</th>
                    <th>WAYBILL STATUS</th>
                    <th>WAYBILL INDICATOR</th>
                    <th>INVOICE STATUS</th>
                    <th>WAYBILL COLLECTION DATE</th>
                    <th>DATE INVOICE</th>
                    <th>DATE PAID</th>
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
                        if(count($waybillstatuses)){
                            foreach($waybillstatuses as $waybillChecker){
                                if($waybillChecker->trip_id == $trip->id && $waybillChecker->waybill_status == TRUE) {
                                    $bgcolor = '#fff';
                                    $color = '#000';
                                    $textdescription = 'AT HQ';
                                    break;
                                } else {
                                    $now = time();
                                    $gatedout = strtotime($trip->gated_out);;
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
                                continue;
                            }
                        }
                        else{
                            $bgcolor = '';
                            $textdescription = 'Waybill Status Not Updated';
                            $color= '#000';
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
                        <td class="text-center">
                            '.$trip->trip_id.'
                            <div class="list-icons">
                                                            
                                <a href="way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                    <i class="icon-file-check"></i>
                                </a>

                                <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                    <i class="icon-calendar52"></i>
                                </a>

                                <span class="list-icons-item">';
                                    if($trip->tracker < 5){
                                        $tabledata.='<i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>';
                                    } else {
                                    $tabledata.='<i class="icon icon-checkmark2" title="Gated Out"></i>';
                                    }
                                $tabledata.='</span>
                                
                                <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                    <i class="icon-file-eye"></i>
                                </a>
                            </div>
                            
                        </td>
                        <td>'.$trip->loading_site.'</td>
                        <td class="text-center font-weight-semibold">';
                            foreach($tripWaybills as $salesNo){
                                if($trip->id == $salesNo->trip_id){
                                $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                                '.strtoupper($salesNo->sales_order_no).'<br>
                                </a>';
                                }
                            }
                        $tabledata.='</td>
                        <td>'.strtoupper($trip->truck_no).'</td>
                        <td>'.strtoupper($trip->account_officer).'</td>
                        <td>'.ucfirst($trip->first_name).' '.ucfirst($trip->last_name).'</td>
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
                                     strtoupper($tabledata.=$invoiceNo->invoice_no).'<br>';
                                }
                            }
                            
                        $tabledata.='</td>
                        <td>'.strtoupper($trip->customers_name).'</td>
                        <td class="text-center">'.$trip->customer_no.'</td>
                        <td>'.$trip->customer_address.'</td>
                        <td>'.strtoupper($trip->exact_location_id).'</td>
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
                        <td class="font-weight-semibold">'.$current_stage.'</td>
                        <td>';
                            foreach($waybillstatuses as $waybillstatus){
                                if($waybillstatus->trip_id == $trip->id){
                                    $tabledata.=$waybillstatus->comment;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>';                        
                        $tabledata.='<td class="text-center">';
                            foreach($invoiceCriteria as $invoiceStatus){
                                if($invoiceStatus->trip_id == $trip->id && $invoiceStatus->invoice_status == TRUE){
                                    $tabledata.='<span class="badge badge-primary">INVOICED</span>';
                                    break;
                                }
                            }
                            
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $collectionDate) {
                                if(($collectionDate->trip_id == $trip->id) && ($collectionDate->waybill_status == TRUE)){
                                    $tabledata.=$collectionDate->updated_at;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $dateInvoiced){
                                if(($dateInvoiced->trip_id == $trip->id)){
                                    $tabledata.=$dateInvoiced->date_invoiced;
                                }
                            }
                        $tabledata.='</td>
                        <td>';
                            foreach($invoiceCriteria as $datePaid){
                                if($datePaid->trip_id == $trip->id && $datePaid->paid_status == TRUE){
                                    $tabledata.=$datePaid->updated_at;
                                }
                            }
                        $tabledata.='</td>

                        
                    </tr>';
                    
                    }
                } else {   
                    $tabledata.='<tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>';
                }       

            $tabledata.='</tbody>
        </table>';

        return $loadingbay.'`'.$tabledata;
    }


    public function sortByloadingSiteandClient(Request $request) {
        $client_id = $request->client_id;
        $loading_site_id = $request->loading_site_id;

        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $invoiceCriteria = tripWaybillStatus::GET();

        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.first_name, h.last_name FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN users h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.user_id = h.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND a.client_id = "'.$client_id.'" AND a.loading_site_id = "'.$loading_site_id.'" ORDER BY a.trip_id ASC  '
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
                    <td>FIELD OPS</td>
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
                        if(count($waybillstatuses)){
                            foreach($waybillstatuses as $waybillChecker){
                                if($waybillChecker->trip_id == $trip->id && $waybillChecker->waybill_status == TRUE) {
                                    $bgcolor = '#fff';
                                    $color = '#000';
                                    $textdescription = 'AT HQ';
                                    break;
                                } else {
                                    $now = time();
                                    $gatedout = strtotime($trip->gated_out);;
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
                                continue;
                            }
                        }
                        else{
                            $bgcolor = '';
                            $textdescription = 'Waybill Status Not Updated';
                            $color= '#000';
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
                        <td class="text-center">
                            '.$trip->trip_id.'
                            <div class="list-icons">
                                                            
                                <a href="way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                    <i class="icon-file-check"></i>
                                </a>

                                <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                    <i class="icon-calendar52"></i>
                                </a>

                                <span class="list-icons-item">';
                                    if($trip->tracker < 5){
                                        $tabledata.='<i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>';
                                    } else {
                                    $tabledata.='<i class="icon icon-checkmark2" title="Gated Out"></i>';
                                    }
                                $tabledata.='</span>
                                
                                <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                    <i class="icon-file-eye"></i>
                                </a>
                            </div>
                            
                        </td>
                       <td>'.strtoupper($trip->loading_site).'</td>
                        <td class="text-center font-weight-semibold">';
                            foreach($tripWaybills as $salesNo){
                                if($trip->id == $salesNo->trip_id){
                                $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                                '.strtoupper($salesNo->sales_order_no).'<br>
                                </a>';
                                }
                            }
                        $tabledata.='</td>
                        <td>'.strtoupper($trip->truck_no).'</td>
                        <td>'.strtoupper($trip->account_officer).'</td>
                        <td>'.ucwords($trip->first_name).' '.ucwords($trip->last_name).'</td>
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
                                    strtoupper($tabledata.=$invoiceNo->invoice_no).'<br>';
                                }
                            }
                            
                        $tabledata.='</td>
                        <td>'.strtoupper($trip->customers_name).'</td>
                        <td class="text-center">'.$trip->customer_no.'</td>
                        <td>'.$trip->customer_address.'</td>
                        <td>'.strtoupper($trip->exact_location_id).'</td>
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
                        
                        <td>'.$this->eventdetails($tripEvents, $trip, 'location_one_comment').'</td>
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
                        <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>';                        
                        $tabledata.='<td class="text-center">';
                            foreach($invoiceCriteria as $invoiceStatus){
                                if($invoiceStatus->trip_id == $trip->id && $invoiceStatus->invoice_status == TRUE){
                                    $tabledata.='<span class="badge badge-primary">INVOICED</span>';
                                    break;
                                }
                            }
                            
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $collectionDate) {
                                if(($collectionDate->trip_id == $trip->id) && ($collectionDate->waybill_status == TRUE)){
                                    $tabledata.=$collectionDate->updated_at;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $dateInvoiced){
                                if(($dateInvoiced->trip_id == $trip->id)){
                                    $tabledata.=$dateInvoiced->date_invoiced;
                                }
                            }
                        $tabledata.='</td>
                        <td>';
                            foreach($invoiceCriteria as $datePaid){
                                if($datePaid->trip_id == $trip->id && $datePaid->paid_status == TRUE){
                                    $tabledata.=$datePaid->updated_at;
                                }
                            }
                        $tabledata.='</td>

                    </tr>';
                    
                    }
                } else {   
                    $tabledata.='<tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>';
                }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;

    }

    public function sortByTracker(Request $request) {
        $tracker = $request->tracker;
        $orders = $this->tripQueryBuilder($tracker, 'tracker');
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $invoiceCriteria = tripWaybillStatus::GET();


        $tabledata = '<table class="table table-bordered">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
                <tr class="font-weigth-semibold">
                    <th class="text-center">#</th>
                    <th class="text-center">KAID</th>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO.</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th>ACCOUNT OFFICER</th>
                    <th>FIELD OPS</th>
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
                    <th>CURRENT STAGE</th>
                    <th>WAYBILL STATUS</th>
                    <th>WAYBILL INDICATOR</th>
                    <th>INVOICE STATUS</th>
                    <th>WAYBILL COLLECTION DATE</th>
                    <th>DATE INVOICE</th>
                    <th>DATE PAID</th>
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
                        if(count($waybillstatuses)){
                            foreach($waybillstatuses as $waybillChecker){
                                if($waybillChecker->trip_id == $trip->id && $waybillChecker->waybill_status == TRUE) {
                                    $bgcolor = '#fff';
                                    $color = '#000';
                                    $textdescription = 'AT HQ';
                                    break;
                                } else {
                                    $now = time();
                                    $gatedout = strtotime($trip->gated_out);;
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
                                continue;
                            }
                        }
                        else{
                            $bgcolor = '';
                            $textdescription = 'Waybill Status Not Updated';
                            $color= '#000';
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
                        <td class="text-center">
                            '.$trip->trip_id.'
                            <div class="list-icons">
                                                            
                                <a href="way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                    <i class="icon-file-check"></i>
                                </a>

                                <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                    <i class="icon-calendar52"></i>
                                </a>

                                <span class="list-icons-item">';
                                    if($trip->tracker < 5){
                                        $tabledata.='<i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>';
                                    } else {
                                    $tabledata.='<i class="icon icon-checkmark2" title="Gated Out"></i>';
                                    }
                                $tabledata.='</span>
                                
                                <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                    <i class="icon-file-eye"></i>
                                </a>
                            </div>
                            
                        </td>
                        <td>'.strtoupper($trip->loading_site).'</td>
                        <td class="text-center font-weight-semibold">';
                            foreach($tripWaybills as $salesNo){
                                if($trip->id == $salesNo->trip_id){
                                $tabledata.='<a href="assets/img/waybills/.'.$salesNo->photo.'" target="_blank" title="View waybill '.$salesNo->sales_order_no.'">
                                '.strtoupper($salesNo->sales_order_no).'<br>
                                </a>';
                                }
                            }
                        $tabledata.='</td>
                        <td>'.strtoupper($trip->truck_no).'</td>
                        <td>'.strtoupper($trip->account_officer).'</td>
                        <td>'.ucwords($trip->first_name).' '.ucwords($trip->last_name).'</td>
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
                                    strtoupper($tabledata.=$invoiceNo->invoice_no).'<br>';
                                }
                            }
                            
                        $tabledata.='</td>
                        <td>'.strtoupper($trip->customers_name).'</td>
                        <td class="text-center">'.$trip->customer_no.'</td>
                        <td>'.$trip->customer_address.'</td>
                        <td>'.strtoupper($trip->exact_location_id).'</td>
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
                        <td class="font-weight-semibold">'.$current_stage.'</td>
                        <td>';
                            foreach($waybillstatuses as $waybillstatus){
                                if($waybillstatus->trip_id == $trip->id){
                                    $tabledata.=$waybillstatus->comment;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>';                        
                        $tabledata.='<td class="text-center">';
                            foreach($invoiceCriteria as $invoiceStatus){
                                if($invoiceStatus->trip_id == $trip->id && $invoiceStatus->invoice_status == TRUE){
                                    $tabledata.='<span class="badge badge-primary">INVOICED</span>';
                                    break;
                                }
                            }
                            
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $collectionDate) {
                                if(($collectionDate->trip_id == $trip->id) && ($collectionDate->waybill_status == TRUE)){
                                    $tabledata.=$collectionDate->updated_at;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $dateInvoiced){
                                if(($dateInvoiced->trip_id == $trip->id)){
                                    $tabledata.=$dateInvoiced->date_invoiced;
                                }
                            }
                        $tabledata.='</td>
                        <td>';
                            foreach($invoiceCriteria as $datePaid){
                                if($datePaid->trip_id == $trip->id && $datePaid->paid_status == TRUE){
                                    $tabledata.=$datePaid->updated_at;
                                }
                            }
                        $tabledata.='</td>
                    </tr>';
                    
                    }
                } else {   
                    $tabledata.='<tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>';
                }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;
    }

    public function sortByTransporters(Request $request) {
        $transporter_id = $request->transporter_id;
        $orders = $this->tripQueryBuilder($transporter_id, 'transporter_id');
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $invoiceCriteria = tripWaybillStatus::GET();

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
                    <th>CURRENT STAGE</th>
                    <th>WAYBILL STATUS</th>
                    <th>WAYBILL INDICATOR</th>
                    <th>INVOICE STATUS</th>
                    <th>WAYBILL COLLECTION DATE</th>
                    <th>DATE INVOICE</th>
                    <th>DATE PAID</th>
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
                        if(count($waybillstatuses)){
                            foreach($waybillstatuses as $waybillChecker){
                                if($waybillChecker->trip_id == $trip->id && $waybillChecker->waybill_status == TRUE) {
                                    $bgcolor = '#fff';
                                    $color = '#000';
                                    $textdescription = 'AT HQ';
                                    break;
                                } else {
                                    $now = time();
                                    $gatedout = strtotime($trip->gated_out);;
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
                                continue;
                            }
                        }
                        else{
                            $bgcolor = '';
                            $textdescription = 'Waybill Status Not Updated';
                            $color= '#000';
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
                        <td class="text-center">
                            '.$trip->trip_id.'
                            <div class="list-icons">
                                                            
                                <a href="way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                    <i class="icon-file-check"></i>
                                </a>

                                <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                    <i class="icon-calendar52"></i>
                                </a>

                                <span class="list-icons-item">';
                                    if($trip->tracker < 5){
                                        $tabledata.='<i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>';
                                    } else {
                                    $tabledata.='<i class="icon icon-checkmark2" title="Gated Out"></i>';
                                    }
                                $tabledata.='</span>
                                
                                <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                    <i class="icon-file-eye"></i>
                                </a>
                            </div>
                            
                        </td>
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
                        <td>'.strtoupper($trip->exact_location_id).'</td>
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
                        <td class="font-weight-semibold">'.$current_stage.'</td>
                        <td>';
                            foreach($waybillstatuses as $waybillstatus){
                                if($waybillstatus->trip_id == $trip->id){
                                    $tabledata.=$waybillstatus->comment;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>';                        
                        $tabledata.='<td class="text-center">';
                            foreach($invoiceCriteria as $invoiceStatus){
                                if($invoiceStatus->trip_id == $trip->id && $invoiceStatus->invoice_status == TRUE){
                                    $tabledata.='<span class="badge badge-primary">INVOICED</span>';
                                    break;
                                }
                            }
                            
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $collectionDate) {
                                if(($collectionDate->trip_id == $trip->id) && ($collectionDate->waybill_status == TRUE)){
                                    $tabledata.=$collectionDate->updated_at;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $dateInvoiced){
                                if(($dateInvoiced->trip_id == $trip->id)){
                                    $tabledata.=$dateInvoiced->date_invoiced;
                                }
                            }
                        $tabledata.='</td>
                        <td>';
                            foreach($invoiceCriteria as $datePaid){
                                if($datePaid->trip_id == $trip->id && $datePaid->paid_status == TRUE){
                                    $tabledata.=$datePaid->updated_at;
                                }
                            }
                        $tabledata.='</td>
                    </tr>';
                    
                    }
                } else {   
                    $tabledata.='<tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>';
                }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;
    }

    public function sortByWaybillstatus(Request $request) {
        $waybill_status = $request->waybill_status;
        $orders = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.first_name, h.last_name FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN users h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.user_id = h.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND a.id IN (SELECT trip_id from tbl_kaya_trip_waybill_statuses WHERE waybill_status = '.$waybill_status.')'
            )
        );
        $tripWaybills = tripWaybill::GET();
        $tripEvents = tripEvent::ORDERBY('current_date', 'DESC')->GET();
        $waybillstatuses = tripWaybillStatus::GET();

        $invoiceCriteria = tripWaybillStatus::GET();


        $tabledata = '<table class="table table-bordered">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee; ">
                <tr class="font-weigth-semibold">
                    <th class="text-center">#</th>
                    <th class="text-center">KAID</th>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO.</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th>ACCOUNT OFFICER</th>
                    <th>FIELD OPS</th>
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
                        if(count($waybillstatuses)){
                            foreach($waybillstatuses as $waybillChecker){
                                if($waybillChecker->trip_id == $trip->id && $waybillChecker->waybill_status == TRUE) {
                                    $bgcolor = '#fff';
                                    $color = '#000';
                                    $textdescription = 'AT HQ';
                                    break;
                                } else {
                                    $now = time();
                                    $gatedout = strtotime($trip->gated_out);;
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
                                continue;
                            }
                        }
                        else{
                            $bgcolor = '';
                            $textdescription = 'Waybill Status Not Updated';
                            $color= '#000';
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
                        <td class="text-center">
                            '.$trip->trip_id.'
                            <div class="list-icons">
                                                            
                                <a href="way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-warning-600" title="Waybill Status">
                                    <i class="icon-file-check"></i>
                                </a>

                                <a href="trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site).'" class="list-icons-item text-info-600" title="Add Events">
                                    <i class="icon-calendar52"></i>
                                </a>

                                <span class="list-icons-item">';
                                    if($trip->tracker < 5){
                                        $tabledata.='<i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>';
                                    } else {
                                    $tabledata.='<i class="icon icon-checkmark2" title="Gated Out"></i>';
                                    }
                                $tabledata.='</span>
                                
                                <a href="trip-overview/'.$trip->trip_id.'" class="list-icons-item text-secondary-600" title="Trip History">
                                    <i class="icon-file-eye"></i>
                                </a>
                            </div>
                            
                        </td>
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
                        <td>'.ucwords($trip->first_name).' '.ucwords($trip->last_name).'</td>
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
                        <td>'.strtoupper($trip->exact_location_id).'</td>
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
                        <td class="text-center" style="background:'.$bgcolor.'; color:'.$color.'">'.$textdescription.'</td>';                        
                        $tabledata.='<td class="text-center">';
                            foreach($invoiceCriteria as $invoiceStatus){
                                if($invoiceStatus->trip_id == $trip->id && $invoiceStatus->invoice_status == TRUE){
                                    $tabledata.='<span class="badge badge-primary">INVOICED</span>';
                                    break;
                                }
                            }
                            
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $collectionDate) {
                                if(($collectionDate->trip_id == $trip->id) && ($collectionDate->waybill_status == TRUE)){
                                    $tabledata.=$collectionDate->updated_at;
                                }
                            }
                        $tabledata.='</td>
                        <td class="text-center">';
                            foreach($waybillstatuses as $dateInvoiced){
                                if(($dateInvoiced->trip_id == $trip->id)){
                                    $tabledata.=$dateInvoiced->date_invoiced;
                                }
                            }
                        $tabledata.='</td>
                        <td>';
                            foreach($invoiceCriteria as $datePaid){
                                if($datePaid->trip_id == $trip->id && $datePaid->paid_status == TRUE){
                                    $tabledata.=$datePaid->updated_at;
                                }
                            }
                        $tabledata.='</td>
                    </tr>';
                    
                    }
                } else {   
                    $tabledata.='<tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>';
                }       

            $tabledata.='</tbody>
        </table>';

        return $tabledata;

    }

    function tripQueryBuilder($client_id, $field_name) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.driver_first_name, c.driver_last_name, c.driver_phone_number, c.motor_boy_first_name, c.motor_boy_last_name, c.motor_boy_phone_no, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage, h.first_name, h.last_name FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g JOIN users h ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id AND a.user_id = h.id WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND a.'.$field_name.' = "'.$client_id.'" ORDER BY a.trip_id DESC '
            )
        );
        return $query;
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
