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
        $selectedMonth =  $request->selected_month;
        $gatedOutTripsforselectedMonth = trip::WHERE('month', $selectedMonth)->WHERE('tracker', '>=', 5)->GET()->COUNT();
        $targetForSelectedMonth = target::WHERE('current_month', $selectedMonth)->GET('target');

        return [$gatedOutTripsforselectedMonth, $targetForSelectedMonth];
    }
    public function gatedOutMonthsCompare(Request $request){
        
        $resultforfirstMonth = trip::WHERE('month', $request->firstMonth)->WHERE('tracker','>=', 5)->WHERE('year', date('Y'))->GET()->COUNT();

        $resultforsecondMonth = trip::WHERE('month', $request->secondMonth)->WHERE('tracker', '>=', 5)->WHERE('year', date('Y'))->GET()->COUNT();

        return [$resultforfirstMonth, $resultforsecondMonth];
    }

    public function monthlyLoadingSite(Request $request){
        return $this->loadingSiteCriteria('WHERE', 'month', $request->selected_month);
    }

    public function loadingSiteBySpecificDay(Request $request){
        return $this->loadingSiteCriteria('WHEREDATE', 'gated_out', $request->choosen_day);
    }

    public function loadingSiteByweekRange(Request $request){
        return $this->loadingSiteCriteria('WHEREBETWEEN', 'gated_out', [$request->weekOne, $request->weekTwo]);
    }

    function loadingSiteCriteria($objectClause, $columnName, $expectedrequest){
        $allLoadingSites = loadingSite::SELECT('id', 'loading_site')->ORDERBY('loading_site', 'ASC')->GET();
        foreach($allLoadingSites as $loadingSite){
            $countDailyTripByLoadingSite[] = trip::WHERE('loading_site_id', $loadingSite->id)->$objectClause($columnName, $expectedrequest)->WHERE('tracker', '>=', 5)->GET()->COUNT();
            $loading_sites[] = $loadingSite->loading_site;
        }

        return [$countDailyTripByLoadingSite, ['`'], $loading_sites];
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

    function getClientStatus($client, $field_value){
        $query = trip::WHERE('client_id', $client)->WHERE('tracker', $field_value)->GET()->COUNT();
        return $query;
    }
}
