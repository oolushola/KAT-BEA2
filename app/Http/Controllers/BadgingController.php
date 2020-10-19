<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Badging;
use App\tripWaybill;


class BadgingController extends Controller
{
    function defaultQuerySorter($condition) {
        $query = DB::SELECT(
            DB::RAW(
                'SELECT a.id, trip_id, exact_location_id, truck_no FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b ON a.truck_id = b.id WHERE a.id '.$condition.' (SELECT trip_id FROM tbl_kaya_trip_badgings) AND client_id = 3'
            )
        );
        return $query;
    }

    function waybillInfoGetters($trips) {
        $waybills = [];
        foreach($trips as $tripObject) {
            $waybill  = tripWaybill::SELECT('trip_id', 'sales_order_no', 'invoice_no')->WHERE('trip_id', $tripObject->id)->GET();
            if(count($waybill)) {
                [$waybills[]] = $waybill;
            }
        }
        return $waybills;
    }

    public function showTrips() {
        $availableTrips = $this->defaultQuerySorter('NOT IN');
        $badgedTrips = $this->defaultQuerySorter('IN');
        $availableTripsWaybill = $this->waybillInfoGetters($availableTrips);
        $badgedTripsWaybill = $this->waybillInfoGetters($badgedTrips);
        return view('/finance.badging', 
            compact(
                'availableTrips', 
                'badgedTrips', 
                'availableTripsWaybill', 
                'badgedTripsWaybill'
            )
        );
    }

    public function badgeTruck(Request $request) {
        $tripIdListings = $request->availableTrucks;
        foreach($tripIdListings as $key => $trip_id) {
            Badging::CREATE([ 'client_id' => 3,  'trip_id' => $trip_id ]);
        }
        return $this->truckBadger();
    } 

    public function removeBadgedTruck(Request $request) {
        $tripIdListings = $request->badgedTrips;
        foreach($tripIdListings as $key => $trip_id) {
            $trip = Badging::WHERE('trip_id', $trip_id)->GET()->LAST();
            $trip->DELETE();
        }
        return $this->truckBadger();
    }

    function truckBadger() {
        $availableTrips = $this->defaultQuerySorter('NOT IN');
        $badgedTrips = $this->defaultQuerySorter('IN');
        $availableTripsWaybill = $this->waybillInfoGetters($availableTrips);
        $badgedTripsWaybill = $this->waybillInfoGetters($badgedTrips);

        $response = '<div class="row">
            <div class="col-md-5">
                &nbsp;
                <div class="card" >
                    <div class="table-responsive" style="max-height:1050px">
                        <table class="table table-bordered" class="badgeAndAvailableTrips">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold" colspan="4">AVAILABLE TRIPS</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllLeftText">
                                        Select all available trips
                                    </td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;"  class="badgeAndAvailableTrips">';
                                if(count($availableTrips)) {
                                    $count = 0;
                                    foreach($availableTrips as $key => $trip) {
                                    $count++; 
                                    if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } 
                                        $response.='<tr class="'.$cssStyle.'">
                                            <td><input type="checkbox" value="'.$trip->id.'" class="availableTrips" name="availableTrucks[]" /></td>
                                            <td>'.$trip->trip_id.'</td>
                                            <td>
                                            <span class="font-weight-semibold text-primary">'.$trip->truck_no.'</span>';
                                            foreach($availableTripsWaybill as $atw) {
                                                if($atw->trip_id == $trip->id) {
                                                    $response.='<span class="d-block mt-1">'.$atw->sales_order_no.', '.$atw->invoice_no.'</span>';
                                                }
                                            }
                                            $response.='</td>
                                            <td>'.$trip->exact_location_id.'</td>
                                        </tr>';
                                    }
                                }
                                else {
                                    $response.='<tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You do not have any trips to badge</td>
                                    </tr>';
                                }
                            $response.='</tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                &nbsp;
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="badgeTruck">BADGED
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeBadgedTruck">REMOVE <i class="icon-point-left ml-2"></i></button>
                </div>
            </div>

            <div class="col-md-5">
                &nbsp;
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <td class="table-primary font-weight-bold" colspan="4">BADGED TRIPS</td>
                            </tr>
                            <tr>
                                <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all badged trips</td>
                            </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="badgeAndAvailableTrips">';
                                if(count($badgedTrips)) {
                                    $counter = 0;
                                    foreach($badgedTrips as $key => $badgeTrip) {
                                    $counter++; 
                                    if($counter % 2 == 0) { $css = 'table-success'; } else { $css = ''; }
                                        $response.='<tr class="'.$css.'">
                                            <td><input type="checkbox" class="badgedTrips" name="badgedTrips[]" value="'.$badgeTrip->id.'" /></td>
                                            <td>'.$badgeTrip->trip_id.'</td>
                                            <td>
                                                <span class="font-weight-semibold text-primary">'.$badgeTrip->truck_no.'</span>';
                                                foreach($badgedTripsWaybill as $btw) {
                                                    if($btw->trip_id == $badgeTrip->id) {
                                                        $response.='<span class="d-block mt-1">'.$btw->sales_order_no.', '.$btw->invoice_no.'</span>';
                                                    }
                                                }
                                            $response.='</td>
                                            <td>'.$badgeTrip->exact_location_id.'</td>
                                        </tr>';
                                    }
                                }
                                else {
                                $response.='<tr class="table-success" style="font-size:10px">
                                    <td colspan="2" class="font-weight-semibold">You\'ve not baddged in any trip yet.</td>
                                </tr>';
                                }
                            $response.='</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>';

        return $response;
    }
}
