<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\client;
use App\AccountManagerTarget;
use Illuminate\Support\Facades\DB;
use App\User;
use App\ClientAccountManager;

class camtController extends Controller
{
    public function clientTargetSetter()
    {
        $clientsDefault = DB::SELECT(
            DB::RAW(
                'SELECT a.id, a.company_name, b.target FROM tbl_kaya_clients a LEFT JOIN tbl_kaya_account_manager_targets b ON a.id = b.client_id WHERE client_status = "1" AND current_year = "'.date('Y').'" AND current_month = "'.date('m').'"  ORDER BY company_name ASC'
            )
        );
        if(count($clientsDefault) <= 0) {
            $clients = client::SELECT('id', 'company_name')->WHERE('client_status', "1")->ORDERBY('company_name', 'ASC')->GET();
        }
        else{
            $clients = $clientsDefault;
        }

        $users = User::SELECT('id', 'first_name', 'last_name')
            ->ORDERBY('first_name', 'ASC')
            ->WHERE('role_id', '<=', 6)
            ->WHERE('status', TRUE)
            ->GET();
        $clientele = client::SELECT('id', 'company_name')->ORDERBY('company_name')->WHERE('client_status', '=', '1')->GET();
        return view('transportation.camt.clientTarget', compact('clients', 'users', 'clientele'));
    }

    public function clientAccountTarget(Request $request)
    {
        $clientIds = $request->client_id;
        $targets = $request->target;
        foreach($clientIds as $key => $client_id) {
            if(isset($targets[$key]) && $targets[$key] != ''){
               $accountTarget = AccountManagerTarget::firstOrNew([
                    'current_year' => date('Year'),
                    'current_month' => date('m'),
                    'client_id' => $client_id,
                ]);
                $accountTarget->target = $targets[$key];
                $accountTarget->save();
            }
        }
        return 'added';
    }

    public function clientAccountManager(Request $request)
    {
        return $this->responseLogger($request->user);
    }

    public function assignClientAccountManager(Request $request) {
        foreach($request->clientele as $key => $client) {
            ClientAccountManager::CREATE([
                'user_id' => $request->user,
                'client_id' => $client
            ]);
        }
        return $this->responseLogger($request->user);
    }

    public function removeAssignedAccountManager(Request $request) 
    {
        foreach($request->assignedClientele as $key => $client) {
           $recid = ClientAccountManager::WHERE('user_id', $request->user)->WHERE('client_id', $client)->GET()->FIRST();
           $recid->DELETE();
        }
        return $this->responseLogger($request->user);
    }

    function responseLogger($user)
    {
        $availableClientele = DB::SELECT(
            DB::RAW(
                'SELECT id, company_name, client_alias FROM tbl_kaya_clients WHERE id NOT IN (SELECT client_id FROM tbl_kaya_client_account_manager) AND client_status = "1" ORDER BY company_name ASC'
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
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="5">CLIENTELE</td>
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
                            <tbody style="font-size:10px;" class="assignClient">';
                                if(count($availableClientele)) {
                                    $count = 0;
                                    foreach($availableClientele as $key => $client) {
                                        $count++; 
                                        if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } 
                                        $response.='<tr class="'.$cssStyle.'">
                                            <td>
                                                <input type="checkbox" value="'.$client->id.'" class="availableClient" name="clientele[]" />
                                            </td>
                                            <td>'.$client->company_name.'</td>
                                        </tr>';
                                    }
                                }
                                else {
                                    $response.='
                                    <tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You do not have any client yet</td>
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
                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="assignClient">ASSIGN
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeClient">REMOVE <i class="icon-point-left ml-2"></i></button>
                </div>
            </div>';

            $assignedClientele = DB::SELECT(
                DB::RAW(
                    'SELECT id, company_name, client_alias FROM tbl_kaya_clients WHERE id IN (SELECT client_id FROM tbl_kaya_client_account_manager WHERE user_id = "'.$user.'") AND client_status = "1" ORDER BY company_name ASC'
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
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="4">ASSIGNED CLIENTS</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all assigned clients</td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="badgeAndAvailableTrips">';
                                if(count($assignedClientele)) {
                                    $count = 0;
                                    foreach($assignedClientele as $key => $assignedclient) {
                                        $count++; 
                                        if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } 
                                        $response.='<tr class="'.$cssStyle.'">
                                            <td>
                                                <input type="checkbox" value="'.$assignedclient->id.'" class="assignedClient" name="assignedClientele[]" />
                                            </td>
                                            <td>'.$assignedclient->company_name.'</td>
                                        </tr>';
                                    }
                                }
                                else{
                                    $response.='
                                    <tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You\'ve not assigned any client yet.</td>
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
