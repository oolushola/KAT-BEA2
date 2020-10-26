<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Auth;
use App\trip;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function uploadProfilePhoto(Request $request) {
        $recid = User::findOrFail(base64_decode($request->user));
        if($request->hasFile('file')){
            $photo = $request->file('file');
            $name = str_slug($request->fullname).$request->user.'.'.$photo->getClientOriginalExtension();
            $destination_path = public_path('assets/img/users/');
            $profilePhotoPath = $destination_path."/".$name;
            $photo->move($destination_path, $name);
            $recid->photo = $name;
            $recid->save();
            return 'uploaded';
        }
    }

    public function changePassword(Request $request) {
        
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required_with:confirm_new_password|min:6',
            'confirm_new_password' => 'required'
        ]);
        $user = User::findOrFail(base64_decode($request->userIdentification));
        if(Hash::check($request->old_password, $user->password)) {
            $newPassword = Hash::make($request->new_password);
            $user->password = $newPassword;
            $user->save();
            return 'changed';
        }
        else{
            return 'wrongpass';
        }
    }

    public function lastTripId(Request $request) {
        return $lastTripId = trip::SELECT('trip_id')->GET()->LAST();
    }

    public function statusChecker($tracker) { 
        if($tracker == 1){ $current_stage = 'GATED IN';}
        if($tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
        if($tracker == 3){ $current_stage = 'LOADING';}
        if($tracker == 4){ $current_stage = 'DEPARTURE';}
        if($tracker == 5){ $current_stage = 'GATED OUT';}
        if($tracker == 6){ $current_stage = 'ON JOURNEY';}
        if($tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
        if($tracker == 8){ $current_stage = 'OFFLOADED';}
        return $current_stage;
    }

    public function tripFinders(Request $request) {
        $rangeFrom = $request->rangeFrom;
        $rangeTo = $request->rangeTo;
        
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT trip_id, loading_site, truck_no, transporter_name, exact_location_id, gated_out, tracker FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_trucks c  JOIN tbl_kaya_transporters d ON a.loading_site_id = b.id AND a.truck_id = c.id AND a.transporter_id = d.id WHERE trip_id BETWEEN "'.$rangeFrom.'" AND "'.$rangeTo.'" '
            )
        );
        return $res = $this->finderResponse($trips);
    }

    public function searchTripFinder(Request $request) {
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.id, trip_id, `exact_location_id`, truck_no, transporter_name, loading_site, gated_out, tracker
                FROM tbl_kaya_trips a JOIN tbl_kaya_trucks b JOIN tbl_kaya_transporters c JOIN tbl_kaya_loading_sites d ON a.transporter_id = c.id AND a.truck_id = b.id AND a.loading_site_id = d.id WHERE (MATCH(trip_id, transporter_name) AGAINST("+'.$request->search.'" IN BOOLEAN MODE)) OR (truck_no LIKE "'.$request->search.'%") '
            )
        );
        return $res = $this->finderResponse($trips);
    }


    function finderResponse($tripLog) {
        $response = '<table class="table table-bordered" id="exportTableDataFinder">
            <thead style="font-size:11px; background:#000; color:#fff">
                <tr>
                    <th>KAID</th>
                    <th>LOADING SITE</th>
                    <th>TRUCK NO</th>
                    <th>TRANSPORTER</th>
                    <th>DESTINATION</th>
                    <th class="text-center">GATE OUT</th>
                    <th>CURRENT STAGE</th>
                </tr>
            </thead>
            <tbody style="font-size:10px" class="font-weight-semibold">';
            if(count($tripLog)){
                $counter = 0;
                foreach($tripLog as $key=> $trip) {
                    $counter++;
                    $counter % 2 == 0 ? $css = ' table-success ' : $css = ' ';
                    $response.='<tr class="'.$css.'">
                        <td>'.$trip->trip_id.'</td>
                        <td>'.strtoupper($trip->loading_site).'</td>
                        <td>'.strtoupper($trip->truck_no).'</td>
                        <td>'.$trip->transporter_name.'</td>
                        <td>'.$trip->exact_location_id.'</td>
                        <td class="text-center">'.date('d/m/Y H:i:s', strtotime($trip->gated_out)).'</td>
                        <td>'.$this->statusChecker($trip->tracker).'</td>
                    </tr>';
                }
            }
            else{
                $response.='<tr>
                    <td colspan="7" class="font-weight-bold text-danger">Oops! we can\'t find any trip that matches your search</td>
                </tr>';
            }
                
            $response.='</tbody>
        </table>';
        return $response;
    }
}
