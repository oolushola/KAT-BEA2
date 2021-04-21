<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\tripWaybill;
use App\companyProfile;
use App\transporter;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class lpoController extends Controller
{
    public function paginate($items, $perPage = 300, $page = null, $options = []) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $pagination = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        $path = url('/').'/local-purchase-order?page='.$page;
        return $pagination->withPath($path);
    }

    public function index(Request $request) {
        $lposummary = $this->lpoQuery('');
        $waybillinfos = [];
        $waybills = [];
        foreach($lposummary as $lpo) {
            $waybills[] = tripWaybill::SELECT('id', 'sales_order_no', 'trip_id')->WHERE('trip_id', $lpo->id)->GET();
        }
        foreach($waybills as $waybillListings) {
            foreach($waybillListings as $waybills) {
                $waybillinfos[] = $waybills;
            }
        }
                
        $myCollectionObj = collect($lposummary);
        $pagination = $this->paginate($myCollectionObj);

        return view('finance.lpo.lpo-listing',
            compact(
                'waybillinfos',
                'pagination'
            )
        );
    }

    public function show(Request $request, $id) {
        $trip_id = $id;
        $lposummary = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id  WHERE a.trip_status = \'1\' AND tracker <> \'0\' AND trip_id = "'.$id.'" ORDER BY a.trip_id ASC'
            )
        );
        $transporterInformation = transporter::WHERE('id', $lposummary[0]->transporter_id)->GET();
        $companyProfile = companyProfile::LIMIT(1)->GET();
        $waybillinfos = tripWaybill::SELECT('id', 'invoice_no', 'sales_order_no', 'trip_id')->ORDERBY('trip_id', 'ASC')->GET();
        return view('finance.lpo.specific-lpo', 
            compact(
                'companyProfile',
                'trip_id',
                'lposummary',
                'transporterInformation',
                'waybillinfos'
            )
        );
    }

    public function filterLpo(Request $request) {
        $dateRangeFrom = $request->dateRangeFrom;
        $dateRangeTo = $request->dateRangeTo;
        $trips = $this->lpoQuery('AND DATE(gated_out) BETWEEN "'.$dateRangeFrom.'" AND "'.$dateRangeTo.'"');

        $waybillinfos = [];
        $waybills = [];
        foreach($trips as $lpo) {
            $waybills[] = tripWaybill::SELECT('id', 'sales_order_no', 'trip_id')->WHERE('trip_id', $lpo->id)->GET();
        }
        foreach($waybills as $waybillListings) {
            foreach($waybillListings as $waybills) {
                $waybillinfos[] = $waybills;
            }
        }

        $response = '
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th class="text-center">TRIP ID</th>
                    <th class="text-center">GATED OUT</th>
                    <th>TRANSPORTER</th>
                    <th>CUSTOMER</th>
                    <th>PRODUCT</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th class="text-center">S.O. NUMBER</th>
                    <th class="text-center">AMOUNT</th>
                </tr>
            </thead>
            <tbody>';
                $count = 0;
                if(count($trips)) {
                    foreach($trips as $lpo) {
                    $count++;
                        $response.='
                        <tr class="hover" style="font-size:11px; cursor:pointer" >
                            <td class="text-center">
                            <a href="/local-purchase-order/'.$lpo->trip_id.'" target="_blank">
                            '.$lpo->trip_id.'
                            </a>
                            </td>
                            <td class="text-center">
                                '.date('m-d-Y', strtotime($lpo->gated_out)).'
                            </td>
                            <td>'.$lpo->transporter_name.'</td>
                            <td>'.$lpo->customers_name.'</td>
                            <td>'.$lpo->product.'</td>
                            <td class="text-center">'.$lpo->truck_no.'</td>
                            <td class="text-center">';
                                foreach($waybillinfos as $salesOrderNumber) {
                                    if($salesOrderNumber->trip_id == $lpo->id) {
                                        $response.= $salesOrderNumber->sales_order_no;
                                    }
                                }
                            $response.='
                            </td>
                            <td class="text-center">&#x20a6;'.number_format($lpo->transporter_rate, 2).'</td>
                        </tr>
                        ';
                    }
                }
                else {

                }
            $response.='
            </tbody>
        </table>';

        return $response;
        
    }


    function lpoQuery($clause) {
        $trips = DB::SELECT(
            DB::RAW(
                'SELECT a.*, b.loading_site, c.`driver_first_name`, c.`driver_last_name`, c.`driver_phone_number`, c.`motor_boy_first_name`, c.`motor_boy_last_name`, c.`motor_boy_phone_no`, d.transporter_name, d.phone_no, e.product, f.truck_no, g.truck_type, g.tonnage FROM tbl_kaya_trips a JOIN tbl_kaya_loading_sites b JOIN tbl_kaya_drivers c JOIN tbl_kaya_transporters d JOIN tbl_kaya_products e JOIN tbl_kaya_trucks f JOIN tbl_kaya_truck_types g ON a.loading_site_id = b.id AND a.driver_id = c.id AND a.transporter_id = d.id AND a.product_id = e.id AND a.truck_id = f.id AND f.truck_type_id = g.id  WHERE a.trip_status = TRUE AND tracker >= 5 '.$clause.' ORDER BY a.trip_id DESC'
            )
        );
        return $trips;
    }
}
