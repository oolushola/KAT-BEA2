<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use App\loadingSite;
use App\OpsLoadingSite;

class fieldOpsLoadingSiteController extends Controller
{
    public function pairfiedOpsLoadingSite() {
        $loadingSites = loadingSite::GET();
        $people = User::WHERE('status', TRUE)->WHERE('role_id', '>=', 3)->GET();
        
        return view('field-ops.assignLoadingSite', compact('people', 'loadingSites'));
    }

    public function fetchLoadingSitePersonPair(request $request) {
        return $this->responseLogger($request->person);
    }

    public function pairPersonLoadingSite(Request $request) {
        foreach($request->loadingSites as $key => $loadingSite) {
            OpsLoadingSite::CREATE([
                'user_id' => $request->person,
                'loading_site_id' => $loadingSite
            ]);
        }
        return $this->responseLogger($request->person);
    }

    public function removePairedLoadingSite(Request $request) 
    {
        foreach($request->pairedLoadingSites as $key => $loadingSite) {
           $recid = OpsLoadingSite::WHERE('user_id', $request->person)->WHERE('loading_site_id', $loadingSite)->GET()->FIRST();
           $recid->DELETE();
        }
        return $this->responseLogger($request->person);
    }

    function responseLogger($user)
    {
        $loadingSites = DB::SELECT(
            DB::RAW(
                'SELECT id, loading_site FROM tbl_kaya_loading_sites WHERE id NOT IN (SELECT loading_site_id FROM tbl_kaya_pair_ops_loading_site WHERE user_id = "'.$user.'") ORDER BY loading_site ASC'
            )
        );
        $response = '
        <div class="row">
            <div class="col-md-5">
                &nbsp;

                <div class="card" >
                    <div class="table-responsive" style="max-height:1050px">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="5">Loading Sites</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllLeftText">
                                        Select all clients
                                    </td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="assingLoadingSite">';
                                if(count($loadingSites)) {
                                    $count = 0;
                                    foreach($loadingSites as $key => $loadingSite) {
                                        $count++; 
                                        if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } 
                                        $response.='<tr class="'.$cssStyle.'">
                                            <td>
                                                <input type="checkbox" value="'.$loadingSite->id.'" class="availableLoadingSite" name="loadingSites[]" />
                                            </td>
                                            <td>'.$loadingSite->loading_site.'</td>
                                        </tr>';
                                    }
                                }
                                else {
                                    $response.='
                                    <tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You do not have any loading site yet</td>
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
                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="assignLoadingSite">PAIR
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeLoadingSite">REMOVE <i class="icon-point-left ml-2"></i></button>
                </div>
            </div>';

            $pairedLoadingSites = DB::SELECT(
                DB::RAW(
                    'SELECT id, loading_site FROM tbl_kaya_loading_sites WHERE id IN (SELECT loading_site_id FROM tbl_kaya_pair_ops_loading_site WHERE user_id = "'.$user.'") ORDER BY loading_site ASC'
                )
            );

            $response.='
            <div class="col-md-5">
                &nbsp;
                <!-- Contextual classes -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="4">Assigned Loading Sites</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all assigned clients</td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="badgeAndAvailableTrips">';
                                if(count($pairedLoadingSites)) {
                                    $count = 0;
                                    foreach($pairedLoadingSites as $key => $pairedLoadingSite) {
                                        $count++; 
                                        if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } 
                                        $response.='<tr class="'.$cssStyle.'">
                                            <td>
                                                <input type="checkbox" value="'.$pairedLoadingSite->id.'" class="pairedLoadingSite" name="pairedLoadingSites[]" />
                                            </td>
                                            <td>'.$pairedLoadingSite->loading_site.'</td>
                                        </tr>';
                                    }
                                }
                                else{
                                    $response.='
                                    <tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You\'ve not assigned any loading site yet.</td>
                                    </tr>';
                                }
                            $response.='
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /contextual classes -->
            </div>
        </div>';

        return $response;
    }

}
