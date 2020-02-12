<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\clientRequest;
use App\Http\Requests\clientProductRequest;
use App\Http\Requests\clientFareRateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\client;
use App\product;
use App\clientProduct;
use App\clientFareRate;
use App\loadingSite;

class clientController extends Controller
{

    public function loadClientStates(Request $request) {
        $answer = '<label>State</label>
                    <select class="form-control form-control-select2" name="state_id" id="state">
                    <option value="0">Choose State Domiciled</option>';
        $country_id = $request->country_id;
         $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = '.$country_id.' ORDER BY state ASC'));
        foreach($states as $state) {
            $answer.='<option value='.$state->regional_state_id.'>'.$state->state.'</option>';
        }     
        $answer.='</select>';

        return $answer;
    }

    public function index() {
        $countries = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_country ORDER BY country ASC'));
        $clients = client::WHERE('client_status', '1')->ORDERBY('company_name', 'ASC')->PAGINATE(15);
        return view('client.create', 
            compact(
                'countries',
                'clients'
            )
        );
    }

    public function store(clientRequest $request) {
        $check = client::WHERE('email', $request->email)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $client = client::CREATE($request->all());
            if($request->parent_company_status == 0){
                $id = $client->id;
                $recid = client::findOrFail($id);
                $recid->parent_company_id = $id;
                $recid->save();
            }
            return 'saved';
        }
    }

    public function edit($id) {
        $countries = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_country ORDER BY country ASC'));
        $clients = client::WHERE('client_status', '1')->ORDERBY('company_name', 'ASC')->PAGINATE(15);
        $recid = client::findOrFail($id);
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state ORDER BY state ASC'));
        return view('client.create', 
            compact(
                'countries',
                'clients',
                'recid',
                'states'
            )
        );
    }

    public function update(clientRequest $request, $id) {
        $check = client::WHERE('email', $request->email)->WHERE('id', '!=', $id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = client::findOrFail($id);
            if($request->parent_company_status == 0){
                $recid->parent_company_id = $id;
            }
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }

    public function clientproduct($client_name, $client_id) {
        $clientName = str_replace('-', ' ', $client_name);
        $products = product::ORDERBY('product', 'ASC')->GET();
        $clientProducts = DB::SELECT(
            DB::RAW(
                    'SELECT a.product_id, b.* FROM tbl_kaya_client_products a JOIN tbl_kaya_products b ON a.`product_id` = b.id WHERE a.client_id = '.$client_id.' ORDER BY product ASC'
            )
        );
        return view('client.clientProduct', 
            compact(
                'clientName',
                'client_id',
                'products',
                'clientProducts'
            )
        );
    }

    public function addClientProducts(clientProductRequest $request) {
        $check = clientProduct::WHERE('client_id', $request->client_id)->WHERE('product_id', $request->product_id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            clientProduct::CREATE($request->all());
            return 'saved';
        }
    }

    public function clientfarerates($client_name, $client_id) {
        $clientName = str_replace('-', ' ', $client_name);
        $recid = client::findOrFail($client_id);
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC '));
        $clientProducts = DB::SELECT(
            DB::RAW(
                    'SELECT a.product_id, b.* FROM tbl_kaya_client_products a JOIN tbl_kaya_products b ON a.`product_id` = b.id WHERE a.client_id = '.$client_id.' ORDER BY product ASC'
            )
        );
        $fareRatings = DB::SELECT(
            DB::RAW(
                'SELECT a.state, b.* FROM tbl_regional_state a JOIN tbl_kaya_client_fare_rates b ON a.regional_state_id = b.to_state_id AND client_id = '.$recid->parent_company_id.' ORDER BY destination ASC'
            )
        );
        
        return view('client.client-fare-rates',
            compact(
                'clientName',
                'states',
                'clientProducts',
                'client_id',
                'client_name',
                'fareRatings',
                'recid'
            )
        );
    }

    public function storeClientRates(clientFareRateRequest $request) {
        $check = clientFareRate::WHERE('client_id', $request->client_id)->WHERE('from_state_id', $request->from_state_id)->WHERE('to_state_id', $request->to_state_id)->WHERE('tonnage', $request->tonnage)->WHERE('amount_rate', $request->amount_rate)->exists();
        if($check) {
            return 'exists';
        }
        else {
            clientFareRate::CREATE($request->all());
            return 'saved';
        }
    }

    public function storeBulkClientRate(Request $request) {
        //get file
        $upload = $request->file('file');
        $filePath = $upload->getRealPath();
        //open and read
        $file = fopen($filePath, 'r');

        // validate
        $header = fgetcsv($file);

        $escapedHeader = [];
        foreach($header as $key => $value) {
            $lowercaseheader  = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '_', $lowercaseheader);
            array_push($escapedHeader, $escapedItem);
        }
         // looping through other columns
         while($columns = fgetcsv($file))
         {
             if($columns[0] == "") {
                 continue;
             }
             
             $data = array_combine($escapedHeader, $columns);

             //setting type
             foreach($data as $key =>  $value) {
                $value = ($key=="from_state_id" || $key="to_state_id")?(integer)$value:(float)$value;
             }
             
             //Table Update
            $from_state_id = $data['from_state_id'];
            $to_state_id = $data['to_state_id'];
            $destination = $data['destination'];
            $tonnage = $data['tonnage'];
            $amount_rate = $data['amount_rate'];
            
            $fareRates = clientFareRate::firstOrNew(['client_id'=>$request->client_id, 'from_state_id' => $from_state_id, 'to_state_id' => $to_state_id, 'destination'=>$destination, 'tonnage'=>$tonnage]);
            $fareRates->from_state_id = $from_state_id;
            $fareRates->to_state_id = $to_state_id;
            $fareRates->destination = $destination;
            $fareRates->tonnage = $tonnage;
            $fareRates->amount_rate = $amount_rate;
            $fareRates->save();
         }
         return 'saved';

    }

    public function editclientrate($client_name, $client_id, $id) {
        $clientName = str_replace('-', ' ', $client_name);
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC '));
        $clientProducts = DB::SELECT(
            DB::RAW(
                    'SELECT a.product_id, b.* FROM tbl_kaya_client_products a JOIN tbl_kaya_products b ON a.`product_id` = b.id WHERE a.client_id = '.$client_id.' ORDER BY product ASC'
            )
        );
        $fareRatings = DB::SELECT(
            DB::RAW(
                'SELECT a.state, b.* FROM tbl_regional_state a JOIN tbl_kaya_client_fare_rates b ON a.regional_state_id = b.to_state_id AND client_id = '.$client_id.' '
            )
        );
        $recid = clientFareRate::findOrFail($id);
        return view('client.client-fare-rates',
            compact(
                'clientName',
                'states',
                'clientProducts',
                'client_id',
                'client_name',
                'fareRatings',
                'recid'
            )
        );
    }

    public function updateClientRates(clientFareRateRequest $request, $id) {
        $check = clientFareRate::WHERE('client_id', $request->client_id)->WHERE('from_state_id', $request->from_state_id)->WHERE('to_state_id', $request->to_state_id)->WHERE('tonnage', $request->tonnage)->WHERE('amount_rate', $request->amount_rate)->WHERE('client_id', $request->client_id)->exists();
        if($check) {
            return 'exists';
        }
        else {
            $recid = clientFareRate::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }

    public function clientloadingsite($client_name, $client_id) {
        $clientName = str_replace('-', ' ', $client_name);
        $states = DB::SELECT(DB::RAW('SELECT * FROM tbl_regional_state WHERE regional_country_id = \'94\' ORDER BY state ASC '));
        $loadingsites = loadingSite::ORDERBY('loading_site', 'ASC')->GET();

        return view('client.assignclientloadingsite',
            compact(
                'clientName',
                'client_id',
                'states',
                'loadingsites'
            )
        );
    }

    public function getloadingsitesperstate(Request $request) {
        $clientID = $request->client_id;
        $stateID  = $request->state_id;
        $clientName = $request->client_name;
        return $this->assignedContent($clientID, $stateID, $clientName);

    }

    public function assignLoadingSite(Request $request) {
        $client_id = $request->client_id;
        $state_id = $request->state_id;
        $clientName = $request->client_name;
        $loading_site_array = $request->loading_site;
            foreach($loading_site_array as $loading_site_id) {
                DB::INSERT(DB::RAW('INSERT INTO tbl_kaya_client_loading_sites (client_id, state_id, loading_site_id) VALUES ('.$client_id.', '.$state_id.', '.$loading_site_id.')'));
            }
        return $this->assignedContent($client_id, $state_id, $clientName);
    }

    public function removeLoadingSite(Request $request) {
        $client_id = $request->client_id;
        $state_id = $request->state_id;
        $clientName = $request->client_name;
        $assignedLoadingSite = $request->loading_site_right;
            foreach($assignedLoadingSite as $loading_site_id) {
                DB::INSERT(DB::RAW('DELETE FROM tbl_kaya_client_loading_sites WHERE client_id = '.$client_id.' AND state_id = '.$state_id.' AND loading_site_id = '.$loading_site_id.''));
            }
        return $this->assignedContent($client_id, $state_id, $clientName);
    }

    public function assignedContent($client_id, $state_id, $clientName) {
        $result ='<div class="row">
            <div class="col-md-5">
            &nbsp;
                <div class="card">
                <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody style="font-size:10px;">
                                <tr>
                                    <td class="table-primary font-weight-semibold" colspan="3">
                                        Assign loading sites to '.ucwords($clientName).'
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="2" id="selectAllLeftText">
                                        Select all available loading sites
                                    </td>
                                </tr>';
                                $leftPanelQuery = DB::SELECT(
                                    DB::RAW(
                                        'SELECT * FROM tbl_kaya_loading_sites WHERE state_domiciled = '.$state_id.' AND id NOT IN (SELECT loading_site_id FROM tbl_kaya_client_loading_sites WHERE state_id = '.$state_id.' AND client_id = '.$client_id.')'
                                    )
                                );
                                $counter = 0;
                                if(count($leftPanelQuery)) {
                                    foreach($leftPanelQuery as $warehouse) {
                                    $counter += 1;
                                    $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                                    $result.='<tr class="'.$css.'" style="font-size:10px">
                                        <td>
                                            <input type="checkbox" class="loadingSiteLeft" name="loading_site[]" value='.$warehouse->id.'>
                                        </td>
                                        <td>'.strtoupper($warehouse->loading_site).'</td>
                                    </tr>';
                                    }
                                } 
                                else {
                                    $result.='<tr>
                                        <td colspan="3">No loading site available to assign</td>
                                    </tr>';
                                }
                            $result.='</tbody>
                        </table>
                    </div>
                </div>

            </div>';

            

            $result.='<div class="col-md-2">
            &nbsp;
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary" id="assignLoadingSite">
                        Assign
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="button" class="btn btn-danger" id="removeAssignedLoadingSite">Remove 
                        <i class="icon-point-left ml-2"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-5">
            &nbsp;

                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody style="font-size:10px;">
                                <tr>
                                    <td class="table-primary font-weight-semibold" colspan="3">Assigned loading sites to '.ucwords($clientName).'          </td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                    <td class="table-info font-weight-semibold" colspan="2" id="assignedRightText">Select all assigned loading sites</td>
                                </tr>';
                                $rightPanelQuery = DB::SELECT(
                                    DB::RAW(
                                        'SELECT * FROM tbl_kaya_loading_sites WHERE state_domiciled = '.$state_id.' AND id IN (SELECT loading_site_id FROM tbl_kaya_client_loading_sites WHERE state_id = '.$state_id.' AND client_id = '.$client_id.')'
                                    )
                                );
                                $counter = 0;
                                if(count($rightPanelQuery)) {
                                    foreach($rightPanelQuery as $warehouseasigned) {
                                    $counter += 1;
                                    $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                                    $result.='<tr class="'.$css.'" style="font-size:10px">
                                        <td>
                                            <input type="checkbox" class="loadingSiteRight" name="loading_site_right[]" value='.$warehouseasigned->id.'>
                                        </td>
                                        <td>'.strtoupper($warehouseasigned->loading_site).'</td>
                                    </tr>';
                                    }
                                } 
                                else {
                                    $result.='<tr>
                                        <td colspan="3">You\'ve not assigned any loading site for this client</td>
                                    </tr>';
                                }
                            $result.='</tbody>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>';

    return $result; 
    }

    public function detailedSpecificClientRate($id, $clientName) {
        $clientName = str_replace('-', ' ', $clientName);
        $clientRates = DB::SELECT(
            DB::RAW(
                'SELECT DISTINCT a.destination, b.state from tbl_kaya_client_fare_rates a JOIN tbl_regional_state b ON a.to_state_id = b.regional_state_id WHERE a.client_id = "'.$id.'" ORDER BY a.destination ASC'
            )
        );
        $ratings = clientFareRate::ORDERBY('destination', 'ASC')->ORDERBY('tonnage', 'ASC')->GET();
        return view('client.client-explicit-pricing', compact('clientName', 'clientRates', 'ratings'));
    }


}
