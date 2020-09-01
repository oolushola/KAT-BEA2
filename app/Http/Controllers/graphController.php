<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
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
use App\transporterRate;
use App\tripPayment;
use App\bulkPayment;
use Auth;
use App\User;
use App\cargoAvailability;
use App\target;

class graphController extends Controller
{
    public function specificMonthTarget(Request $request){
        $selectedMonth =  $request->month;
        $selectedYear = $request->year;
        
        $gatedOutTripsForSelectedMonth = trip::WHEREYEAR('gated_out', $selectedYear)
            ->WHEREMONTH('gated_out', $selectedMonth)
            ->WHERE('tracker', '>=', 5)
            ->WHERE('trip_status', 1)
            ->GET()
            ->COUNT();

        $targetForSelectedMonth = target::SELECT('target')
        ->WHEREYEAR('created_at', $selectedYear)
        ->WHEREMONTH('created_at', $selectedMonth)
        ->FIRST();
        if(!$targetForSelectedMonth) {
            $targetForSelectedMonth = 150;
        }
        else {
            $targetForSelectedMonth = $targetForSelectedMonth;
        }
        return [$gatedOutTripsForSelectedMonth, $targetForSelectedMonth];
    }

    
    public function gatedOutMonthsCompare(Request $request){
        $year = date('Y');
        $firstMonth = $request->firstMonth;
        $secondMonth = $request->secondMonth;
        $resultforfirstMonth = trip::WHEREYEAR('gated_out', $year)
        ->WHEREMONTH('gated_out', $firstMonth)
        ->WHERE('tracker','>=', 5)
        ->WHERE('trip_status', 1)
        ->GET()
        ->COUNT();

        $resultforsecondMonth = trip::WHEREYEAR('gated_out', $year)
        ->WHEREMONTH('gated_out', $secondMonth)
        ->WHERE('tracker', '>=', 5)
        ->WHERE('trip_status', 1)
        ->GET()
        ->COUNT();
        return [$resultforfirstMonth, $resultforsecondMonth];
    }

    public function monthlyLoadingSite(Request $request){
        $choosenMonth = $request->selected_month;
        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();
        foreach($allLoadingSites as $loadingSite){
            $loading_sites[] = strtoupper($loadingSite->loading_site);
            $tripLoadingSiteCounts[] = trip::WHEREYEAR('gated_out', date('Y'))
            ->WHEREMONTH('gated_out', $choosenMonth)
            ->WHERE('loading_site_id', $loadingSite->id)
            ->WHERE('trip_status', 1)
            ->WHERE('tracker', '>=', 5)
            ->GET()
            ->COUNT();
        }
        return [$tripLoadingSiteCounts, $loading_sites];
    }

    public function loadingSiteBySpecificDay(Request $request){
        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();
        foreach($allLoadingSites as $loadingSite){
            $loading_sites[] = strtoupper($loadingSite->loading_site);
            $tripLoadingSiteCounts[] = trip::WHEREDATE('gated_out', $request->choosen_day)
            ->WHERE('loading_site_id', $loadingSite->id)
            ->WHERE('trip_status', 1)
            ->WHERE('tracker', '>=', 5)
            ->GET()
            ->COUNT();
        }
        return [$tripLoadingSiteCounts, $loading_sites];
    }

    public function loadingSiteByweekRange(Request $request){
        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();
        foreach($allLoadingSites as $key => $loadingSite){
            $loading_sites[] = strtoupper($loadingSite->loading_site);
            [$tripCounts[]] = DB::SELECT(
                DB::RAW(
                    'SELECT COUNT(*) AS counts FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$request->weekOne.'" AND "'.$request->weekTwo.'" AND loading_site_id = "'.$loadingSite->id.'" AND trip_status = 1 AND tracker >= 5 '
                )
            );
            $tripLoadingSiteCounts[] = $tripCounts[$key]->counts;
        }
        
        return [$tripLoadingSiteCounts, $loading_sites];
    }

    function loadingSiteCriteria($objectClause, $columnName, $expectedrequest){
        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();
        foreach($allLoadingSites as $loadingSite){
            $countDailyTripByLoadingSite[] = trip::WHERE('loading_site_id', $loadingSite->id)->$objectClause($columnName, $expectedrequest)->WHERE('tracker', '>=', 5)->GET()->COUNT();
            $loading_sites[] = $loadingSite->loading_site;
        }

        return [$countDailyTripByLoadingSite, $loading_sites];
    }

    public function clientTripStatus(Request $request){
        $client_id = $request->client_id;
        $gateInCount = $this->getClientStatus($client_id, '1');
        $arrivalatloadingBay = $this->getClientStatus($client_id, '2');
        $onJourney = trip::WHERE('client_id', $client_id)->WHERE('tracker', '>=', 5)->WHERE('tracker', '<=', 6)->GET()->COUNT();
        $atDestination = $this->getClientStatus($client_id, '7');
        $offloadedTrips = $this->getClientStatus($client_id, '8');
        
        return [$gateInCount, $arrivalatloadingBay, $onJourney, $atDestination, $offloadedTrips];

    }

    public function tripsForTheMonth(Request $request) {
        $currentYear = $request->year;
        $currentMonth = $request->month;
        $tripRecordsForTheMonth = $this->totalTripsForTheCurrentMonth($currentMonth, $currentYear);

        foreach($tripRecordsForTheMonth as $key => $monthTripId) {
                $monthlyGateOut[] = tripWaybill::SELECT('id', 'trip_id', 'sales_order_no', 'invoice_no', 'photo')->WHERE('trip_id', $monthTripId->id)->GET();
            }

        foreach($monthlyGateOut as $key => $values) {
            foreach($values as $value) {
                $monthWaybillRecord[] = $value;
            }
        }

        $data = $this->records($tripRecordsForTheMonth, $monthWaybillRecord);
        return $data;
     
    }

    public function chartDateRange(Request $request) {
        $dateFrom = $request->dateFrom;
        $dateTo = $request->currentDay;
        $noOfGatedOutTripForCurrentWeek = $this->specificDateRangeCount('COUNT(*)',  'weeklygateout', $dateFrom, $dateTo);
        $specificDataRecord = $this->specificDateRangeData($dateFrom, $dateTo);

        if(count($specificDataRecord)) {
            foreach($specificDataRecord as $key => $trips) {
                $waybill_listings[] = tripWaybill::SELECT('id', 'trip_id', 'sales_order_no', 'invoice_no', 'photo')->WHERE('trip_id', $trips->id)->GET();
            }
            $waybills = [];

            foreach($waybill_listings as $key => $values) {
                foreach($values as $value) {
                    $waybills[] = $value;
                }
            }
        }
        else {
            $waybill_listings = [];
            $waybills = [];
        }
        $data = $this->records($specificDataRecord, $waybills);
        return [$noOfGatedOutTripForCurrentWeek, $data];
    }

    function getClientStatus($client, $field_value){
        $query = trip::WHERE('client_id', $client)->WHERE('tracker', $field_value)->GET()->COUNT();
        return $query;
    }
    
    function totalTripsForTheCurrentMonth($currentMonth, $currentYear) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker >= \'5\' AND YEAR(gated_out) = "'.$currentYear.'" AND MONTH(gated_out) = "'.$currentMonth.'" ORDER BY a.gated_out DESC'
            )
        );
        return $query;
    }

    function specificDateRangeData($start, $finish) {
        $specificDateRecord = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g  ON a.loading_site_id = b.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id WHERE a.trip_status = \'1\' AND tracker >= \'5\' AND DATE(gated_out) BETWEEN "'.$start.'" AND "'.$finish.'" ORDER BY a.trip_id DESC'
            )
        );
        return $specificDateRecord;
    }

    public function waybillDetails() {
        $record = $this->totalTripsForTheCurrentMonth();
        foreach($record as $data) {
            echo $data;
        }
    }

    function specificDateRangeCount($condition, $alias, $start, $finish){
        return DB::SELECT(
            DB::RAW(
                'SELECT '.$condition.' AS '.$alias.'  FROM tbl_kaya_trips WHERE DATE(gated_out) BETWEEN "'.$start.'" and "'.$finish.'" and tracker >= 5'
            )
        );
    }


    function records($trips, $wabillResult) {
        $response ='<table class="table table-striped table-hover">
            <div class="table-responsive">
                <thead>
                    <tr class="table-success" style="font-size:10px;">
                        <th class="font-weight-bold">SN</th>
                        <th class="font-weight-bold text-center">GATE OUT DETAILS</th>
                        <th class="font-weight-bold">TRUCK</th>
                        <th class="font-weight-bold">WAYBILL DETAILS</th>
                        <th class="font-weight-bold">CONSIGNEE DETAILS</th>
                    </tr>
                </thead>
                <tbody id="monthlyGatedOutData" style="font-size:10px; font-weight:normal">';
                    $count = 1;
                    if(count($trips)) {
                        foreach($trips as $trip) {
                        $response.='<tr>
                            <td width="5%">('.$count++.')</td>
                            <td class="text-center">
                                <p class="font-weight-bold" style="margin:0;">'.$trip->trip_id.'</p>
                                <p>'.$trip->loading_site.' <br>'.date("d-m-Y", strtotime($trip->gated_out)).'<br>'.date("H:i A", strtotime($trip->gated_out)).'</p>
                            </td>
                            <td>
                                <span class="text-primary"><b>'.$trip->truck_no.'</b></span>
                                <p style="margin:0"><b>Truck Type</b>: '.$trip->truck_type.' '.$trip->tonnage / 1000 .' T</p>
                                <p style="margin:0"><b>Transporter</b>: '.$trip->transporter_name.', '.$trip->phone_no.'</p>
                            </td>
                            
                            <td>
                                <p style="margin:0" class="font-weight-bold">';
                                foreach($wabillResult as $tripWaybill) {
                                    if($trip->id == $tripWaybill->trip_id) {
                                    $response.='<a href="assets/img/waybills/'.$tripWaybill->photo.'" target="_blank" title="View waybill '.$tripWaybill->sales_order_no.'" >
                                        <p class="mb-1">'.$tripWaybill->sales_order_no.' '.$tripWaybill->invoice_no.'</p>
                                    </a>';
                                    }
                                }
                            $response.='</p>';
                                
                            $response.='</td>
                            <td>
                                <p class="font-weight-bold" style="margin:0">'.$trip->customers_name.'</p>
                                <p  style="margin:0">Location: '.$trip->exact_location_id.'</p>
                                <p  style="margin:0">Product: '. $trip->product.'</p>

                            </td>
                        </tr>';
                        }
                    }
                    else{
                        $response.='<tr>
                            <td colspan="4">No trip is available</td>
                        </tr>';
                    }
                $response.='</tbody>
            </div>
        </table>';
        return $response;
    }





}
