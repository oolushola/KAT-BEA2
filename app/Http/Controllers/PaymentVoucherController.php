<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\PaymentVoucher;
use App\PaymentVoucherDesc;
use Illuminate\Support\Facades\DB;
use App\user;
use App\companyProfile;
use App\ExpenseType;
use App\Department;
use App\expenses;
use App\ExpensesBreakdown;
use Hash;

class PaymentVoucherController extends Controller
{
    public function index() {
        $userDepartment = Auth::user()->department_id;
        $expenseTypes = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_kaya_expense_types WHERE id IN (SELECT expense_type_id FROM tbl_kaya_department_expense_types WHERE department_id = "'.$userDepartment.'") ORDER BY expense_type ASC'
            )
        );
        $possibleOwners = User::SELECT('first_name', 'last_name', 'id')->WHERE('department_id', $userDepartment)->WHERE('status', TRUE)->GET();
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
        return view('finance.vouchers.refunds', compact('paymentVoucher', 'voucherArray', 'expenseTypes', 'possibleOwners'));
    }

    public function store(Request $request) {
        $userId = Auth::user()->id;
        $myDepartmentHod = DB::SELECT(
            DB::RAW(
                'SELECT head_of_department FROM tbl_kaya_departments WHERE id IN (SELECT department_id FROM users WHERE id = 1)'
            )
        );
        if($request->description[0] != '') {
            $paymentVoucher = PaymentVoucher::CREATE([
                'requested_by' => $userId,
                'request_timestamps' => Date('Y-m-d H:i:s A'),
                'hod' => $myDepartmentHod[0]->head_of_department
            ]);
            $uniqueId = 'payvou'.base64_encode($paymentVoucher->id);
            $uniqueIdValue = explode('=', $uniqueId)[0];
            $paymentVoucher->uniqueId = strtolower($uniqueIdValue);
            $paymentVoucher->save();
            $count = 0;
            foreach ($request->description as $key => $paymentDescription) {
                $count++;
                if(isset($paymentDescription) && $request->description[$key] != '') {
                    $expenseType = ExpenseType::findOrFail($request->expenseType[$key]);
                    $paymentVoucherdescription = PaymentVoucherDesc::firstOrNew([
                        'payment_voucher_id' => $paymentVoucher->id,
                        'expense_type' => $expenseType->expense_type,
                        'expense_type_id' => $request->expenseType[$key],
                        'description' => $paymentDescription,
                        'owner' => $request->owner[$key],
                        'amount' => $request->amount[$key]
                    ]);
                    if(!empty($request->attachment[$key])) {
                        $attachment = $request->file('attachment');
                        $name = $paymentVoucher->uniqueId.'-'.md5($count).'.'.$attachment[$key]->getClientOriginalExtension();
                        $destination_path = public_path('assets/img/vouchers/');
                        $voucherPath = $destination_path."/".$name;
                        $attachment[$key]->move($destination_path, $name);
                        $paymentVoucherdescription->attachment = $name;
                    }
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
        $userDepartment = Auth::user()->department_id;
        $expenseTypes = DB::SELECT(
            DB::RAW(
                'SELECT * FROM tbl_kaya_expense_types WHERE id IN (SELECT expense_type_id FROM tbl_kaya_department_expense_types WHERE department_id = "'.$userDepartment.'") ORDER BY expense_type ASC'
            )
        );
        $possibleOwners = User::SELECT('first_name', 'last_name', 'id')->WHERE('department_id', $userDepartment)->WHERE('status', TRUE)->GET();
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
            'recidDesc',
            'expenseTypes',
            'possibleOwners',
            'userDepartment'
            )
        );
    }

    public function update(Request $request, $id) {
        $payment_voucher_id = $request->id;
        if($request->description[0] != '') {
            $myDepartmentHod = DB::SELECT(
                DB::RAW(
                    'SELECT head_of_department FROM tbl_kaya_departments WHERE id IN (SELECT department_id FROM users WHERE id = 1)'
                )
            );
            $voucher = PaymentVoucher::findOrFail($id);
            $voucher->hod = $myDepartmentHod[0]->head_of_department;
            $voucher->save();
            $collections = PaymentVoucherDesc::WHERE('payment_voucher_id', $request->id)->GET(['id']);
            PaymentVoucherDesc::destroy($collections->toArray());
            foreach ($request->description as $key => $desc) {
                if(isset($request->description) && $request->description[$key] != '') {
                    $expenseType = ExpenseType::findOrFail($request->expenseType[$key]);
                    $paymentVoucherUpdate = PaymentVoucherDesc::firstOrNew([
                        'payment_voucher_id' => $request->id,
                        'description' => $desc,
                        'owner' => $request->owner[$key],
                        'amount' => $request->amount[$key],
                        'expense_type' => $expenseType->expense_type,
                        'expense_type_id' => $request->expenseType[$key],
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
        if(Auth::user()->role_id == 1) {
            $getUnverifiedVouchers = PaymentVoucher::WHERE('check_status', FALSE)->WHERE('voucher_status', FALSE)->WHERE('decline_status', FALSE)->GET();
        }
        else {
            $getUnverifiedVouchers = PaymentVoucher::WHERE('hod', Auth::user()->id)->WHERE('check_status', FALSE)->WHERE('voucher_status', FALSE)->WHERE('decline_status', FALSE)->GET();
        }
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
        $myDepartments = Department::WHERE('head_of_department', Auth::user()->id)->GET();
        if(count($myDepartments) == 0) {
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
                            <div class="col-md-6 col-sm-6 col-xs-12" style="max-height:400px; overflow:auto" id="parent'.$voucher->uniqueId.'">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-success font-weight-bold font-size-xs mb-2 paymentBreakdown pointer" id="'.$voucher->uniqueId.'" value="0" title="Click to view breakdown and attachments(if any)">
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
                                                        <span class="mt-1 font-weight-semibold" style="font-size:12px">
                                                            <pre class="d-none voucher'.$voucher->uniqueId.'" style="padding:10px; margin-bottom: 3px; font-size: 11px; font-family:tahoma">
                                                            ('.$count++.') '.$desc->description.' &#x20A6; '. number_format($desc->amount, 2) .'';
                                                            if($desc->attachment) {
                                                                $response.='<a target="_blank" href="assets/img/vouchers/'.$desc->attachment.'"><i class="icon-attachment ml-4"></i></a>';
                                                            }
                                                        
                                                    $response.='</pre></span>';
                                                    
                                                }
                                            }
                                        $response.='
                                            <h5 class="mt-2 font-weight-bold mb-0">Total: &#x20A6;'.number_format($sumTotal, 2).'
                                                <input type="checkbox" name="voucherIds[]" value="'.$voucher->id.'" id="" class="ml-1 paymentVouchers"  >
                                                <span class="badge badge-danger font-size-xs pointer ml-2 declineOneVoucher" value="'.$voucher->id.'" title="'.$voucher->uniqueId.'">Decline</span>
                                            </h5>
                                    </div>
                                </div>
                            </div>';
                    }
                    $response.='
                    <div class="text-right d-block">
                        <input type="hidden" name="declineStatus" value="1" />
                        <span id="loader"></span>
                        <button type="submit" class="btn btn-primary mt-2" id="approveVerifiedPayment">Approve 
                            <i class="icon-stamp ml-2"></i>
                        </button>
                        <button type="submit" class="btn btn-danger mt-2 font-size-sm pointer" id="declineAllVerifiedPayments">Decline  
                            <i class="icon-x ml-2"></i>
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

    public function declinePaymentVoucher(Request $request) {
        $decline_status = $request->declineStatus;
        $voucherId = $request->voucherId;
        if($decline_status == 0) {
            $voucher = PaymentVoucher::findOrFail($voucherId);
            $voucher->check_status = 0;
            $voucher->checked_timestamps = NULL;
            $voucher->checked_by = NULL;
            $voucher->save();
        }
        else{
            if($decline_status == 1) {
                foreach($request->voucherIds as $voucherId) {
                    $voucher = PaymentVoucher::findOrFail($voucherId);
                    $voucher->check_status = 0;
                    $voucher->checked_timestamps = NULL;
                    $voucher->checked_by = NULL;
                    $voucher->save();
                }

            }
        }
        return 'declined';
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
        $paymentVouchers = PaymentVoucher::WHERE('check_status', TRUE)->WHERE('approved_status', TRUE)->WHERE('upload_status', TRUE)->GET();
        $unpaidVouchers = PaymentVoucher::WHERE('check_status', TRUE)->WHERE('approved_status', TRUE)->WHERE('upload_status', FALSE)->GET();
        $voucherListings = [];
        $users = [];
        $voucherDescriptions = [];
        foreach($paymentVouchers as $voucher) {
            $voucherListings[] = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET(); 
            $users[] = User::findOrFail($voucher->requested_by); 
            [$voucherDescriptions[]] = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET(); 
        }
        foreach($voucherListings as $vouchers) {
            foreach($vouchers as $voucher) {
                $voucherArray[] = $voucher;
            }
        }

        $unpaidVoucherListings = [];
        $unpaidVouchersDesc = [];
        $people = [];
        foreach($unpaidVouchers as $voucher) {
            $unpaidVoucherListings[] = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET();
            $people[] = User::findOrFail($voucher->requested_by); 
        }
        foreach($unpaidVoucherListings as $unpaids) {
            foreach($unpaids as $unpaidVoucher) {
                $unpaidVouchersDesc[] = $unpaidVoucher;
            }
        }

        return view('finance.vouchers.voucher', compact('paymentVouchers', 'users', 'unpaidVouchers', 'unpaidVouchersDesc', 'people', 'voucherDescriptions'));
    }

    public function showPaymentVoucher($voucherId) {
        $companyProfile = companyProfile::GET();
        $voucher = PaymentVoucher::WHERE('uniqueId', $voucherId)->GET()->FIRST();
        $voucherDesc = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucher->id)->GET();

        $bankDetails = [];

        foreach($voucherDesc as $desc) {
            $bankDetails = DB::SELECT(
                DB::RAW(
                    'SELECT * FROM users WHERE CONCAT(first_name, \' \', last_name) = "'.$desc->owner.'"'
                )
            );
        }

        $requester = User::findOrFail($voucher->requested_by);
        $approval = User::findOrFail($voucher->approved_by);
        $checker = User::findOrFail($voucher->checked_by);

        $sum = 0;
        $count = 0;
        foreach($voucherDesc as $desc) {
            $sum += $desc->amount;
        }
        $amountInWords = $this->convert_number_to_words($sum).' naira only';

        return view('finance.vouchers.voucher-template', compact('companyProfile', 'voucher', 'voucherDesc', 'requester', 'approval', 'checker', 'amountInWords', 'sum', 'bankDetails'));
    }

    public function uploadPaymentVoucher(Request $request) {
        $voucherIdLists = $request->voucherIds;
        if(count($voucherIdLists) <= 0) {
            return 'aborted';
        }
        else{
            foreach($voucherIdLists as $voucherId) {
                $voucherDesc = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucherId)->GET();
                foreach($voucherDesc as $breakdown) {
                    $expenseBreakDown = ExpensesBreakdown::firstOrNew([
                        'current_year' => date('Y'),
                        'current_month' => date('m'),
                        'category' => $breakdown->expense_type,
                    ]);
                    $expenseBreakDown->amount += $breakdown->amount;

                    $expenses = expenses::firstOrNew([
                        'year' => date('Y'),
                        'month' => date('n'),
                    ]);
                    $expenses->expenses += $breakdown->amount;
                    $expenses->save();
                    $expenseBreakDown->save();
                }
                $paymentVoucher = PaymentVoucher::findOrFail($voucherId);
                $paymentVoucher->upload_status = TRUE;
                $paymentVoucher->upload_by = Auth::user()->id;
                $paymentVoucher->upload_timestamps = Date('Y-m-d H:i:s A');
                $paymentVoucher->voucher_status = TRUE;
            }
            $paymentVoucher->save();
            return 'uploaded';
        }
    }

    public function strikeOutVoucher(Request $request) {
        $id = $request->id;
        $voucher = PaymentVoucher::findOrFail($id);
        $voucher->decline_status = TRUE;
        $voucher->save();

        return 'cancelled';
    }

    public function deleteVoucher(Request $request) {
        $voucherId = $request->id;
        $voucher = PaymentVoucher::findOrFail($voucherId);
        $voucher->delete();
        
        $voucherDescs = PaymentVoucherDesc::WHERE('payment_voucher_id', $voucherId)->GET();
        foreach ($voucherDescs as $key => $desc) {
            $voucherDesc = PaymentVoucherDesc::findOrFail($desc->id);
            $voucherDesc->delete();
        }
        return 'deleted';
    }

    public function newBeneficiary(Request $request) {
        $department_id = Auth::user()->department_id;
        $password = Hash::make(trim($request->first_name));
        $email = trim($request->first_name.'.'.$request->last_name.'@kaya-world.com');
        $user = user::WHERE('account_no', $request->account_no)->exists();

        if($user) {
            return 'recordExists';
        }
        else{
            user::CREATE([
                'first_name' => trim($request->first_name),
                'last_name' => trim($request->last_name),
                'email' => $email,
                'phone_no' => "2347034106000",
                'role_id' => 0,
                'status' =>TRUE,
                'password' => $password,
                'bank_name' => $request->bank_name,
                'account_no' => $request->account_no,
                'account_name' => $request->account_name,
                'department_id' => $department_id
            ]);
        }
        return 'saved';
    }


    function convert_number_to_words($number) {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' & ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );
    
        if (!is_numeric($number)) {
            return false;
        }
    
        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }
    
        if ($number < 0) {
            return $negative . $this->convert_number_to_words(abs($number));
        }
    
        $string = $fraction = null;
    
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
    
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }
    
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
    
        return $string;
    }



 

}
