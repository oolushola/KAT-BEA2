<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\PaymentVoucher;
use App\PaymentVoucherDesc;
use Illuminate\Support\Facades\DB;
use App\user;

class PaymentVoucherController extends Controller
{
    public function index() {
        $paymentVoucher = PaymentVoucher::WHERE('voucher_status', FALSE)->WHERE('requested_by', Auth::user()->id)->GET();
        $voucherListings = [];
        $voucherArray = [];
        foreach($paymentVoucher as $voucher) {
            $voucherListings[] = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET();   
        }
        foreach($voucherListings as $vouchers) {
            foreach($vouchers as $voucher) {
                $voucherArray[] = $voucher;
            }
        }
        return view('finance.vouchers.refunds', compact('paymentVoucher', 'voucherArray'));
    }

    public function store(Request $request) {
        $userId = Auth::user()->id;
        if($request->description[0] != '') {
            $paymentVoucher = PaymentVoucher::CREATE([
                'requested_by' => $userId,
                'request_timestamps' => Date('Y-m-d H:i:s A'),
            ]);
            $uniqueId = 'payvou'.base64_encode($paymentVoucher->id);
            $uniqueIdValue = explode('=', $uniqueId)[0];
            $paymentVoucher->uniqueId = strtolower($uniqueIdValue);
            $paymentVoucher->save();
    
            foreach ($request->description as $key => $paymentDescription) {
                if(isset($paymentDescription) && $request->description[$key] != '') {
                    $paymentVoucherdescription = PaymentVoucherDesc::firstOrNew([
                        'payment_voucher_id' => $paymentVoucher->id,
                        'description' => $paymentDescription,
                        'owner' => $request->owner[$key],
                        'amount' => $request->amount[$key]
                    ]);
                    $paymentVoucherdescription->save();
                }
            }
        }
        else {
            return 'nothing is entered!';
        }
        return 'saved';
    }

    public function edit($id) {
        $paymentVoucher = PaymentVoucher::WHERE('voucher_status', FALSE)->WHERE('requested_by', Auth::user()->id)->GET();
        $recid = PaymentVoucher::findOrFail($id);
        $recidDesc = PaymentVoucherDesc::WHERE('payment_voucher_id', $id)->GET();
        foreach($paymentVoucher as $voucher) {
            $voucherListings[] = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET();   
        }
        foreach($voucherListings as $vouchers) {
            foreach($vouchers as $voucher) {
                $voucherArray[] = $voucher;
            }
        }
        return view('finance.vouchers.refunds', compact(
            'paymentVoucher', 
            'voucherArray',
            'recid',
            'recidDesc'
            )
        );
    }

    public function update(Request $request, $id) {
        $payment_voucher_id = $request->id;
        if($request->description[0] != '') {
            $collections = PaymentVoucherDesc::WHERE('payment_voucher_id', $request->id)->GET(['id']);
            PaymentVoucherDesc::destroy($collections->toArray());

            foreach ($request->description as $key => $desc) {
                if(isset($request->description) && $request->description[$key] != '') {
                    $paymentVoucherUpdate = PaymentVoucherDesc::firstOrNew([
                        'payment_voucher_id' => $request->id,
                        'description' => $desc,
                        'owner' => $request->owner[$key],
                        'amount' => $request->amount[$key]
                    ]);
                    $paymentVoucherUpdate->save();
                }
            }
            return 'updated';
        }
        else{
            return 'Nothing was added';
        }
    }

    public function verifyPaymentVoucher() {
        $user = Auth::user()->verify_payment_access;
        $getUnverifiedVouchers = PaymentVoucher::WHERE('check_status', FALSE)->WHERE('voucher_status', FALSE)->GET();
        $voucherListings = [];
        $users = [];
        foreach($getUnverifiedVouchers as $voucher) {
            $voucherListings[] = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET();
            $users[] = User::findOrFail($voucher->requested_by);   
        }
        $voucherDescArray = [];
        foreach($voucherListings as $vouchers) {
            foreach($vouchers as $voucher) {
                $voucherDescArray[] = $voucher;
            }
        }
        return view('finance.vouchers.verify', compact(
            'getUnverifiedVouchers',
            'voucherDescArray',
            'users'
        ));
    }

    public function verifyPayments(Request $request) {
        $paymentAccess = Auth::user()->verify_payment_access;
        if($paymentAccess != TRUE) {
            return 'accessDenied';
        }
        else {
            foreach($request->voucherIds as $id) {
                $voucher = PaymentVoucher::findOrFail($id);
                $voucher->check_status = TRUE;
                $voucher->checked_by = Auth::user()->id;
                $voucher->checked_timestamps = Date('Y-m-d H:i:s A');
                $voucher->save();
            }
            return 'verified';
        }
    }

    public function getPaymentVoucherApprovals() {
        $user = Auth::user()->verify_payment_access;
        $getUnapprovedVouchers = PaymentVoucher::WHERE('check_status', TRUE)->WHERE('approved_status', FALSE)->WHERE('voucher_status', FALSE)->GET();
        $voucherListings = [];
        $users = [];
        $validator = [];
        foreach($getUnapprovedVouchers as $voucher) {
            $voucherListings[] = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET();
            $users[] = User::findOrFail($voucher->requested_by);  
            $validator[] = User::findOrFail($voucher->checked_by);
        }
        $voucherDescArray = [];
        foreach($voucherListings as $vouchers) {
            foreach($vouchers as $voucher) {
                $voucherDescArray[] = $voucher;
            }
        }

        

        $response= '
        <div class="card-body">
            <div class="row">';
                if(count($getUnapprovedVouchers)) {
                    foreach($getUnapprovedVouchers as $key => $voucher){
                        $response.='
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-success font-weight-bold font-size-xs mb-2">
                                            '.strtoupper($voucher->uniqueId).'
                                            <span class="text-primary font-weight-bold font-size-xs" style="float:right;">
                                                Requested by: '.ucfirst($users[$key]->first_name).'
                                            </span>
                                            <span class="text-primary font-weight-bold font-size-xs d-block mt-1" style="text-align:center;">
                                                Validated By: '.ucfirst($validator[$key]->first_name).' @ '.$voucher->checked_timestamps.'
                                            </span>
                                        </p>';
                                            $count = 1; $sumTotal = 0;
                                            foreach($voucherDescArray as $desc) {
                                                if($desc->payment_voucher_id == $voucher->id) {
                                                    $sumTotal += $desc->amount;
                                                    $response.='
                                                        <span class="d-block mt-1 font-weight-semibold" style="font-size:12px">
                                                        ('.$count++.') '.$desc->description.' &#x20A6; '. number_format($desc->amount, 2) .'
                                                    </span>';
                                                }
                                            }
                                        $response.='
                                            <h5 class="mt-2 font-weight-bold mb-0">Total: &#x20A6;'.number_format($sumTotal, 2).'
                                                <input type="checkbox" name="voucherIds[]" value="'.$voucher->id.'" id="" class="ml-1 paymentVouchers"  >
                                            </h5>
                                    </div>
                                </div>
                            </div>';
                    }
                    $response.='
                    <div class="text-right d-block">
                        <span id="loader"></span>
                        <button type="submit" class="btn btn-primary mt-2" id="approveVerifiedPayment">Approve 
                            <i class="icon-stamp ml-2"></i>
                        </button>
                    </div>';
                }
                
                else {
                    $response.='<h5>Yipee! You do not have any voucher to approve.</h5>';
                }
            $response.='</div>
        </div>';

        return $response;
    }

    public function approvePaymentVouchers(Request $request) {
        if(!count($request->voucherIds)) {
            return 'cantUpdate';
        }
        else{
            foreach($request->voucherIds as $id) {
                $voucher = PaymentVoucher::findOrFail($id);
                $voucher->approved_status = TRUE;
                $voucher->approved_by = Auth::user()->id;
                $voucher->approval_timestamps = Date('Y-m-d H:i:s A');
                $voucher->save();
            }
            return 'approved';
        }
    }

    public function vouchers() {
        $paymentVoucher = PaymentVoucher::WHERE('voucher_status', FALSE)->WHERE('requested_by', Auth::user()->id)->GET();
        foreach($paymentVoucher as $voucher) {
            $users[] = User::findOrFail($voucher->requested_by);   
        }
        foreach($voucherListings as $vouchers) {
            foreach($vouchers as $voucher) {
                $voucherArray[] = $voucher;
            }
        }
        return view('finance.vouchers.voucher', compact('paymentVoucher', 'users'));
    }
}
