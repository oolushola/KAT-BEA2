<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use App\PaymentNotification;

class PaymentNotificationController extends Controller
{
    public function paymentNotifications() {
        $payments = DB::SELECT(
            DB::RAW(
                'SELECT b.id, paid_status, paid_time_stamps, a.trip_id, exact_location_id, truck_no, tonnage, truck_type, loading_site, uploaded_at, first_name, amount, photo, payment_for, transporter_name from tbl_kaya_trips a JOIN tbl_kaya_payment_notifications b JOIN tbl_kaya_trucks c JOIN tbl_kaya_loading_sites d JOIN tbl_kaya_truck_types e JOIN users f JOIN tbl_kaya_transporters g ON a.id = b.trip_id AND c.id = a.`truck_id` AND d.id = a.loading_site_id AND e.id = c.truck_type_id AND b.uploaded_by = f.id AND g.id = a.transporter_id WHERE paid_status = FALSE OR b.`paid_time_stamps` >= (CURDATE() - INTERVAL 1 DAY) ORDER BY b.created_at DESC'
            )
        );

        $answer = '<ul class="media-list">';
        foreach($payments as $uploadedPay)
        {
            $answer.= '
            <li class="media" id="box'.$uploadedPay->id.'">
                <div class="mr-3 position-relative">
                    <img src="/assets/img/users/'.$uploadedPay->photo.'" width="36" height="36" class="rounded-circle" alt="'.$uploadedPay->first_name.'">
                </div>
                <div class="media-body">
                    <div class="media-title">
                        <a href="#">
                            <span class="font-weight-semibold font-size-xs">'.$uploadedPay->payment_for.' of &#x20A6;'.number_format($uploadedPay->amount, 2).'</span>
                            <span class="text-muted float-right font-size-xs">'.Carbon::parse($uploadedPay->uploaded_at)->diffForHumans().'</span>
                        </a>
                    </div>
                    <span class="text-muted font-size-xs font-weight-bold">FOR: '.$uploadedPay->trip_id.'->'.$uploadedPay->truck_no.' '.$uploadedPay->tonnage/1000 .'T, '.$uploadedPay->truck_type.'</span>
                    <span class="text-muted font-size-xs font-weight-bold">Loaded at '.$uploadedPay->loading_site.', To: '.$uploadedPay->transporter_name.'</span>';

                    if(Auth::user()->role_id == 1) {
                        if($uploadedPay->paid_status == FALSE) {
                            $answer.='
                            <div class="d-block">
                                <span class="badge badge-success pointer paidFor paid'.$uploadedPay->id.'" id="'.$uploadedPay->id.'" >PAID</span>
                                <span class="badge badge-danger pointer declineFor declined'.$uploadedPay->id.'" id="'.$uploadedPay->id.'">DECLINE</span>
                            </div>';
                        }
                        else {
                            $answer.='
                                <div class="d-block"><span class="badge">PAID @ '.$uploadedPay->paid_time_stamps.' <i class="icon-checkmark2"></i></span></div>';
                        }
                    }
                    else {
                        if($uploadedPay->paid_status == FALSE) {
                            $labelName = 'PENDING <i class="icon-spinner3 spinner"></i>';
                        }
                        else{
                            $labelName = 'PAID @ '.$uploadedPay->paid_time_stamps.' <i class="icon-checkmark2"></i>';
                        }
                        $answer.='
                        <div class="d-block"><span class="badge">'.$labelName.'</span></div>';
                    }
                $answer.='
                </div>
            </li>';
        }
        $answer.='</ul>';
        return $answer;
    }

    public function approveUploadedPayment(Request $request) {
        $recid = PaymentNotification::findOrFail($request->paymentNotificationId);
        $recid->paid_status = TRUE;
        $recid->paid_time_stamps = date('Y-m-d H:i:s');
        $recid->save();
        return 'approved';
    }

    public function declineUploadedPayment(Request $request) {
        $recid = PaymentNotification::findOrFail($request->paymentNotificationId);
        $recid->DELETE();
        return 'declined';
    }
}
