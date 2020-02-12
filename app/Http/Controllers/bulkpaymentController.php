<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\driversRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\HttpResponse;
use App\transporter;
use App\bulkPayment;
use App\bulkPaymentHistory;


class bulkpaymentController extends Controller
{
    public function index() {
        $transporters = transporter::ORDERBY('transporter_name', 'ASC')->GET();
        $bulkpayments = DB::SELECT(
            DB::RAW(
                'SELECT b.`transporter_name`, a.* FROM `tbl_kaya_bulk_payments` a JOIN `tbl_kaya_transporters` b ON a.transporter_id = b.id'
            )
        );
        return view('finance.bulk-payment.create',
            compact(
                'transporters',
                'bulkpayments'
            )
        );
    }

    public function store(Request $request) {
        $validatedata = $request->validate([
            'transporter_id' => 'required|integer',
            'amount_credited' => 'required|between:0,99.99'
        ]);
        $checkPendingChunkApproval = bulkPayment::WHERE('transporter_id', $request->transporter_id)->WHERE('approval_status', 0)->exists();
        if($checkPendingChunkApproval) {
            return 'cant_proceed';
        } else {
            $chunkRate = bulkPayment::firstOrNew(['transporter_id'=>$request->transporter_id]);
            $chunkRate->amount_credited = $request->amount_credited;
            $chunkRate->date_uploaded = date('Y-m-d, g:i A');
            $chunkRate->remark = $request->remark;
            $chunkRate->approval_status = 0;
            $chunkRate->save();

            //Put mail notification here please...

            $bulkpaymenthistory = bulkPaymentHistory::CREATE([
                'bulk_payment_id' => $chunkRate->id,
                'transporter_id' => $request->transporter_id,
                'amount_credited' => $request->amount_credited,
                'date_uploaded' => date('Y-m-d, g:i A'),
            ]);
            $bulkpaymenthistory->save();
            return 'saved';
        }
    }

    public function edit($id) {
        $transporters = transporter::ORDERBY('transporter_name', 'ASC')->GET();
        $recid = bulkPayment::findOrFail($id);
        $bulkpayments = DB::SELECT(
            DB::RAW(
                'SELECT b.`transporter_name`, a.* FROM `tbl_kaya_bulk_payments` a JOIN `tbl_kaya_transporters` b ON a.transporter_id = b.id'
            )
        );
        return view('finance.bulk-payment.create',
            compact(
                'transporters',
                'bulkpayments',
                'recid'
            )
        );
    }

    public function update(Request $request, $id) {
        $validatedata = $request->validate([
            'transporter_id' => 'required|integer',
            'amount_credited' => 'required|between:0,99.99'
        ]);
        $checkPendingChunkApproval = bulkPayment::WHERE('transporter_id', $request->transporter_id)->WHERE('approval_status', 0)->WHERE('id', '!=', $id)->exists();
        if($checkPendingChunkApproval) {
            return 'cant_proceed';
        } else {
            $recid = bulkPayment::findOrFail($id);
            $recid->UPDATE($request->all());
            return 'updated';
        }
    }

    public function approvePayment(Request $request) {
        $bulkpayments = $request->approvePayment;
        foreach($bulkpayments as $key => $chunk_payment_id) {
           $chunkPay = bulkPayment::findOrFail($chunk_payment_id);
           $balance =  $chunkPay->balance + $chunkPay->amount_credited;
           $chunkPay->UPDATE([
               'balance' => $balance,
               'approval_status' => 1,
               'date_approved' => date('Y-m-d, g:i A')
           ]);
        }

        return 'approved';
        
    }
}
