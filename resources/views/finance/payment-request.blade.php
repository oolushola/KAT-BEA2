@extends('layout')

@section('title')Payment Request @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
    padding: 5px;
}
</style>
@stop

@section('main')

<?php
function getFieldValue($arrayofrecords, $master, $fieldname) {
    foreach($arrayofrecords as $object) {
        $trip_id = str_replace('KAID', '', $master->trip_id);
        if($object->trip_id == $trip_id){
            echo $object->$fieldname.'<br>';
        }
    }
}

function getPaymentInitiator($arrayRecord, $master) {
    foreach($arrayRecord as $object) {
        if(($object->transporter_id == $master->transporter_id) && ($object->balance > 0)){
            return "&nbsp; &nbsp;<sup><i class=\"icon-stars text-primary-400 text-right\"></i></sup>";
        }
    }
}

?>

<form method="POST" name="frmPayment" id="frmPayment" action="{{URL('bulk-full-payment')}}">
    @csrf
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex text-primary" id="advanceRequestLog">
            <h4><i class="icon-coins mr-2"></i> <span class="font-weight-semibold">Advance Request Log</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="page-title d-flex" id="balanceRequestLog">
            <h4><i class="icon-wallet mr-2"></i> <span class="font-weight-semibold">Balance Request Log</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>

<div class="row" id="advancecarrier">
    <div class="col-md-12">
    &nbsp;

        <div class="card">
            <div class='row card-header header-elements-inline'>
                <h6 class="card-title font-weight-semibold">Advance Payment Request Log</h6>
            </div>
            <span class="col-md-12 d-block">
                @if(count($allpendingadvanceRequests) > 0)
                <button class="btn btn-primary font-size-sm font-weight-bold mb-3" id="approveAdvancePayment"><i class="icon-checkmark2"></i>Complete all advance payment</button>
                @endif

                <a href="{{ URL('payment-top-up') }}" class="btn btn-success font-size-sm font-weight-bold mb-3">
                    <i class="icon-menu-open"></i>Advance Top up Exception
                </a>
            </span>

            <div class="row">
                @if(count($allpendingadvanceRequests))
                <?php $advanceIterator = 1; ?>
                    @foreach($allpendingadvanceRequests as $key => $advancePayment)
                        <section class="col-md-3 mt-2 col-sm-12 col-12 mb-2">
                            <!--<span class="bg-danger font-weight-bold" style="border-radius:100%; padding:15px; margin-left:-20px;">{{ $advanceIterator++ }}</span>-->
                            <div class="card">
                                <div class="table-responsive">
                                    <table class="" width="100%">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <span class="defaultInfo">
                                                        <span class="font-weight-bold">{!! $advancePayment->trip_id !!}</span> 
                                                    </span>
                                                </td>
                                                <td>
                                                    
                                                    @foreach($advanceWaybillInfos as $advanceWaybill)
                                                        @if($advanceWaybill->trip_id == $advancePayment->tripid)
                                                        <a href="{{URL::asset('assets/img/waybills/'.$advanceWaybill->photo)}}" target="_blank" title="View waybill {{$advanceWaybill->sales_order_no}}">
                                                            <span class="badge badge-primary">
                                                                {{ $advanceWaybill->sales_order_no }}
                                                            </span>
                                                        </a>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong class="d-block">Request Info</strong> {{ucfirst($advancePayment->first_name)}} {{ucfirst($advancePayment->last_name)}}

                                                    <span class="d-block font-size-sm text-primary font-weight-semibold">{{ $advancePayment->advance_requested_at }}</span>
                                                
                                                </td>
                                                <td>
                                                    <strong class="d-block">Loading Site</strong>
                                                    {{ $advancePayment->loading_site }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Client Rate <span id="gtvtrLoader"></span>
                                                    <span class="d-block updateClientRate" id="{!! $advancePayment->trip_id !!}">&#x20a6;{{ number_format($advancePayment->client_rate, 2) }}</span>
                                                    <span class="hidden" id="clientRate{!! $advancePayment->trip_id !!}">
                                                        <input type="text" class="d-block" style="width:120px" id="clientRateValue{{$advancePayment->trip_id}}" value="{{$advancePayment->client_rate}}">
                                                        <button class="d-block mt-1 btn btn-primary updateClientRateVal" id="{{$advancePayment->trip_id}}">Update</button>
                                                    </span>
                                                </td>
                                                <td>Transporter Rate 
                                                    <span class="d-block updateTransporterRate" id="{!! $advancePayment->trip_id !!}">&#x20a6;{{ number_format($advancePayment->transporter_rate, 2) }}</span>
                                                    <span class="hidden" id="transporterRate{!! $advancePayment->trip_id !!}">
                                                        <input type="text" class="d-block" style="width:120px" id="transporterRateValue{{$advancePayment->trip_id}}" value="{{$advancePayment->transporter_rate}}">
                                                        <button class="mt-1 btn btn-primary updateTransporterRateVal" id="{{$advancePayment->trip_id}}">Update</button><span id="trLoader"></span>
                                                    </span>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">
                                                    <span class="d-block">Amount</span>
                                                    <p class="text-primary d-block font-weight-bold">&#x20a6;{!! number_format($advancePayment->advance, 2) !!} </p>
                                                </td>
                                                <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">Destination</span>
                                                <p class="text-primary d-block font-weight-bold">{!! $advancePayment->exact_location_id !!}, {!! $advancePayment->state !!}</p>
                                            </td>
                                            </tr>
                                            <tr><td colspan="2">{!! $advancePayment->transporter_name !!}</td></tr>
                                            
                                            <tr>
                                                <td>
                                                <h6 class="mb-0">
                                                    <span class="defaultInfo">
                                                        <span class="text-primary">{!! $advancePayment->truck_no !!}</span>
                                                        <span class="d-block font-size-sm "><strong>Tonnage</strong>: {!! $advancePayment->tonnage/1000 !!}T, 
                                                            {!! $advancePayment->loaded_weight !!}
                                                        </span>
                                                        <span class="d-block font-size-sm "><strong>Product</strong>: {!! $advancePayment->product !!}</span>
                                                        <!--<span class="d-block font-size-sm "><strong>Weight</strong>: {!! $advancePayment->loaded_weight !!}</span>-->
                                                    </span>
                                                </h6>
                                                </td>
                                                <td>
                                                <h6 class="mb-0">
                                                    <span class="defaultInfo">
                                                        <span class="text-primary">{!! $advancePayment->account_number !!}</span>
                                                        <span class="d-block font-size-sm "> {!! $advancePayment->account_name !!}</span>
                                                        <span class="d-block font-size-sm "> {!! $advancePayment->bank_name !!}</span>
                                                    </span>
                                                </h6>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <a href="#advancePaymentExceptionModal" class="badge btn-warning except pointer align-right ml-auto" role="{{$advancePayment->trip_id}}" value="{{$advancePayment->amount}}" id="{{$advancePayment->id}}" data-toggle="modal">Exception</a>
                                                </td>
                                                <td class="font-size-sm font-weight-bold" style="font-size:11px;">
                                                    <input type="checkbox" class="advanceValue" value="{{$advancePayment->id}}" name="approveAdvance[]"> Paid
                                                </td>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    @endforeach
                    

                @else
                    <p class="ml-3 mb-3 font-weight-bold text-danger">You currently do not have any advance payment to approve.</p>
                @endif
            </div>
            @if(count($allpendingadvanceRequests) > 1)
            <div class="ml-2 mb-2">
                <p class="font-weight-semibold">
                    <span>Multiple full payment exception 
                        <input type="radio" class="multipleSelector" name="multipleExceptionSelector" value="1" />
                    </span>
                    <span class="ml-3">Multiple (0) Rate Advance 
                        <input type="radio" class="multipleSelector" name="multipleExceptionSelector" value="2" />
                    </span>    
                </p>
                <button id="bulkFullPayment" class="d-none btn btn-danger font-weight-semibold"><i class="icon-stack"></i>Proceed with multiple full payment </button>

                <button id="multipleZeroAdvance" class="d-none btn btn-warning font-weight-semibold"><i class="icon-stack"></i>Proceed with multiple 0 rate</button>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row d-none" id="balancecarrier">
    <div class="col-md-12">
    &nbsp;

        <div class="card">
            <div class='card-header header-elements-inline'>
                <h6 class="card-title font-weight-semibold">Balance Payment Request Log</h6>
            </div>
            
            <span class="col-md-3 d-block">
                <button class="btn btn-primary font-size-sm font-weight-bold ml-3 mb-3" id="approveBalancePayment"><i class="icon-checkmark2"></i>Complete Balance Payment</button>
            </span>
            
            <div class="row">
                
                @if(count($allpendingbalanceRequests))
                    <?php $balanceIterator = 1; ?>
                    @foreach($allpendingbalanceRequests as $key => $balancePayment)
                        <section class="col-md-4 mt-2 col-sm-12 col-12 mb-4 ml-2">
                            <!--<span class="bg-danger font-weight-bold" style="border-radius:100%; padding:15px; margin-left:-20px;">{{ $balanceIterator++ }}</span>-->
                            <div class="card">
                                <div class="table-responsive">
                                    <table width="100%" style="padding:0; margin:0;">
                                        <tbody>
                                            <tr>
                                                <td class="font-weight-bold">{!! $balancePayment->trip_id !!}</td>
                                                <td>
                                                    <span class="defaultInfo">
                                                        <span class="font-size-sm">Waybill:</span> 
                                                        @foreach($waybillStatuses as $waybillStatus)
                                                            @if($waybillStatus->trip_id == $balancePayment->tripid)
                                                                <span class="badge badge-danger">{{ $waybillStatus->comment }}</span>
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong class="d-block">Request info</strong> 
                                                    {{ucfirst($balancePayment->first_name)}} {{ucfirst($balancePayment->last_name)}}
                                                    <span class="d-block">{{ $balancePayment->balance_requested_at }}</span>
                                                </td>
                                                <td>
                                                    <strong class="d-block">Loading Site</strong>
                                                    {{ $balancePayment->loading_site }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong class="text-warning font-size-sm">Waybill Proof </strong>
                                                    <span class="d-block">
                                                        <?php $count = 0; ?>
                                                        @foreach($offloadedWaybill as $offloadWaybill)
                                                            @if($offloadWaybill->trip_id == $balancePayment->tripid)
                                                            <?php $count++; ?>
                                                                <a class="badge bg-primary" href="{{URL('/assets/img/signedwaybills/'.$offloadWaybill->received_waybill)}}" alt="{{$offloadWaybill->received_waybill}}" target="_blank"><i class="icon-file-eye"></i></a>
                                                            @endif
                                                        @endforeach
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-danger d-block font-size-sm font-weight-bold">Comment</span>
                                                    @foreach($offloadedWaybill as $offloadWaybill)
                                                            @if($offloadWaybill->trip_id == $balancePayment->tripid)
                                                                {{ $offloadWaybill->waybill_remark }}
                                                                @break
                                                            @endif
                                                        @endforeach
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>Transporter Rate: 
                                                        <span class="trForBalanceDefault" id="{{ $balancePayment->trip_id }}">
                                                            &#x20a6;{{ number_format($balancePayment->transporter_rate, 2) }}
                                                        </span>
                                                        <span id="trForBalanceEditable{{ $balancePayment->trip_id }}" class="hidden">
                                                            <input type="text" value="{{ $balancePayment->transporter_rate, 2 }}" style="width:100px" class="changeTransporterAtBalance" title="{{ $balancePayment->id }}" />
                                                        </span>
                                                    </p>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">
                                                    <span class="d-block">Balance</span>
                                                    <p class="text-primary d-block font-weight-bold">
                                                        &#x20a6;{{number_format($balancePayment->balance, 2)}} 
                                                        {!! getPaymentInitiator($chunkPayments, $balancePayment) !!} 
                                                    </p>
                                                </td>
                                                <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">Destination</span>
                                                <p class="text-primary d-block font-weight-bold">{!! $balancePayment->exact_location_id !!}, {!! $balancePayment->state !!}</p>
                                            </td>
                                            <tr>
                                            <tr>
                                                <td colspan="2">{!! $balancePayment->transporter_name !!}</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h6 class="mb-0">
                                                        <span class="defaultInfo">
                                                            <span class="text-primary">{!! $balancePayment->truck_no !!}</span>
                                                            <span class="d-block font-size-sm "><strong>Tonnage</strong>: {!! $balancePayment->tonnage/1000 !!}T</span>
                                                            <span class="d-block font-size-sm "><strong>Product</strong>: {!! $balancePayment->product !!}</span>
                                                        </span>
                                                    </h6>
                                                </td>
                                                <td class="font-weight-bold">
                                                    <h6 class="mb-0">
                                                        <span class="defaultInfo">
                                                            <span class="text-primary">{!! $balancePayment->account_number !!}</span>
                                                            <span class="d-block font-size-sm ">{!! $balancePayment->account_name !!}</span>
                                                            <span class="d-block font-size-sm ">{!! $balancePayment->bank_name !!}</span>
                                                        </span>
                                                    </h6>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td>
                                                    <a href="#balancePaymentExceptionModal" class="badge balanceExcept bg-warning pointer align-right ml-auto" role="{{$balancePayment->trip_id}}" value="{{$balancePayment->exact_location_id}},{{$balancePayment->advance}},{{$balancePayment->balance}},{{$balancePayment->transporter_id}},{{$balancePayment->trip_id}}" id="{{$balancePayment->id}}" data-toggle="modal">Exception</a>
                                                </td>
                                                <td class="font-weight-sm font-weight-bold" style="font-size:9px;"><input type="checkbox" value="{{$balancePayment->id}}" name="approveBalance[]"> Mark as paid</td>
                                        </tbody>
                                        
                                    </table>
                                    
                                </div>
                            </div>
                        </section>
                    @endforeach
                   

                @else
                    <p class="ml-4 mb-3 font-weight-bold text-danger">You currently do not have any balance payment to approve.</p>
                @endif
                
            </div>

           
        </div>


    </div>
</div>

<div class="row">

    <div class="col-md-12">
    &nbsp;

        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title font-weight-semibold">Outstanding Balance Payment Request Log 
                    <span id="viewOutstandingLog" class="text-primary pointer font-size-sm">View <i class="icon-arrow-down5"></i></span> 
                    <span id="collapseOutstandingLog" class="text-danger hidden pointer font-size-sm">Collapse <i class="icon-arrow-up13"></i></span> 
                </h6>
            </div>

            <div class="table-responsive hidden" id="outstandingLogTable">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:10px;">
                            <th>KAID</th>
                            <th>DESTINATION</th>
                            <th>TRANSPORTER</th>
                            <th>TRUCK NO.</th>
                            <th>PRODUCT</th>
                            <th>OUTSTANDING</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>

                    <tbody>
                        
                        <?php $counter = 0; ?>
                        @if(count($allPendingOutstandingBalance))
                            @foreach($allPendingOutstandingBalance as $key => $outstandingPayment)
                            <?php
                                $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';

                            ?>
                                <tr class="{{$css}} font-weight-semibold" style="font-size:10px;">
                                    <td>{{$outstandingPayment->trip_id}}</td>
                                    <td>{{$outstandingPayment->exact_location_id}}, {{$outstandingPayment->state}}</td>
                                    <td>{{$outstandingPayment->transporter_name}}</td>
                                    <td>{{$outstandingPayment->truck_no}}</td>
                                    <td>{{$outstandingPayment->product}}</td>
                                    <td>
                                        &#x20a6;{{number_format($outstandingPayment->outstanding_balance, 2)}}
                                        {!! getPaymentInitiator($chunkPayments, $outstandingPayment) !!}

                                    </td>
                                    <td>
                                        <a href="#outstandingPaymentModal" class="badge payoutstandingBalance bg-warning pointer align-right ml-auto" role="{{$outstandingPayment->trip_id}}" value="{{$outstandingPayment->outstanding_balance}}, {{$outstandingPayment->trip_id}}" id="{{$outstandingPayment->id}}" data-toggle="modal">Pay Now</a>

                                    </td>
                                   
                                </tr>
                            @endforeach
                                <tr class="table-info">
                                    <td colspan="7"><span class="float-right" id="outstandbalanceLoading"></span></td>
                                    
                                </tr>
                        @else
                        <tr>
                            <td colspan="15" class="table-success">No pending payment request</td>
                        </tr>
                        @endif
                    </tbody>
                    
                </table>
            </div>
        </div>


    </div>
</div>

</form>

    
<!-- Modal HTML for Advance Request -->
<div id="advancePaymentExceptionModal" class="modal fade">
    <form method="POST" name="frmAdvanceEception" id="frmAdvanceEception">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Exception for TRIP: <span id="tripIdHolder"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <span id="exceptionLoader"></span>
                    <ul style="margin:0; padding:0;" class="row">
                        <li class="col-md-4 exception exception-default" id="percentageDifference">Use Different % Rate </li>
                        <li class="col-md-4 exception" id="fullPayment">Full Payment</li>
                        <li class="col-md-4 exception" id="manualAdvanceInput">Enter Amount</li>
                        
                        <input type="hidden" value="2" name="advance_exception" id="advanceNavigator">

                    </ul>

                    <div class="row ml-3 mr-3 mt-3 show" id="percentHolder">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Amount (₦)</label>
                                <input type="text" class="form-control" id="totalAmountHolder" disabled>
                                <input type="hidden" id="totalAmount_" name="total_amount" value="" >
                                <input type="hidden" value="" id="payid" name="payid" >
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Advance % Rate</label>
                                <input type="number" class="form-control" name="new_advance_rate" id="newAdvanceRate" min="1" max="99">
                                <span id="advanceRatePreview"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Balance % Rate</label>
                                <input type="number" class="form-control" name="new_balance_rate" id="newBalanceRate" disabled>
                                <span id="balanceRatePreview"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Remark / Comment</label>
                                <input type="text" class="form-control" name="percentile_remark" id="percentileRemark" >
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">&nbsp; &nbsp;</label><br>
                                <button type="submit" class="btn btn-lg btn-primary" id="updatePercentile">Update Changes</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row ml-3 mr-3 mt-3 hidden" id="payInFullContainer">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-semibold">Pay in Full? &nbsp; &nbsp;</label>
                            <input type="checkbox" class="form-control" id="payInFull">
                            <input type="hidden" name="pay_in_full" id="pay_in_full" value="0" >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Remarks / Reason</label>
                            <input type="text" class="form-control" name="fullpayment_remarks" id="fullPaymentRemarks">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-semibold">&nbsp; &nbsp;</label>
                            <button type="submit" class="btn btn-primary" id="updateFullPayment">Save Changes</button>
                        </div>
                    </div>
                </div>

                <div class="row ml-3 mr-3 mt-3 hidden" id="manualAdvancwPaymentHolder">
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-semibold">Actual Amount</label>
                            <input type="text" class="form-control" name="actual_amount" id="actualAmount">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-semibold">Amount to be Paid </label>
                            <input type="text" class="form-control" name="advanceTobeManuallyPaid" id="advanceTobePaid">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-semibold">Balance</label>
                            <input type="text" class="form-control" name="probableBalance" id="probableBabalance" onclick="keyupanalyser()">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="font-weight-semibold">&nbsp; &nbsp;</label>
                            <button type="submit" class="btn btn-primary" id="manualAmountProceed">Save Changes</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>  
    </form>
</div>

<!-- Modal HTML for Balance Request -->
<div id="balancePaymentExceptionModal" class="modal fade">
    <form method="POST" name="frmBalanceException" id="frmBalanceException">
        @csrf
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Balance Exception for TRIP: <span id="balanceTripIdHolder"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                

                <input type="hidden" name="balanceTripId" id="balanceTripId">
                <input type="hidden" name="transporter_id" id="transporterId" >
                <input type="hidden" name="trip_id" id="tripId" >
                
                <div class="modal-body">
                    <span id="exceptionBalanceLoader"></span>

                    <div class="ml-4">
                        <input type="checkbox" id="bitPartBalance" >
                         <span class="font-weight-bold text-danger">Pay Part Amount of Balance</span>
                    </div>

                    <div class="row ml-3 mr-3 mt-3 show" id="wrongRouteHolder">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Destination</label>
                                <input type="text" class="form-control" id="tripDestination" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Advance Paid</label>
                                <input type="number" class="form-control" name="advancePaid" id="advancePaid">
                                <span id="advanceRatePreview"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Supposed Balance</label>
                                <input type="number" class="form-control" name="supposedBalance" id="supposedBalance">
                                <span id="balanceRatePreview"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">State on AX</label>
                                <select class="form-control" name="state_id" id="stateId">
                                    <option value="0">Please Choose</option>
                                    @foreach($states as $state)
                                    <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Destination</label>
                                <div id="exactLocationHolder">
                                    <select class="form-control">
                                        <option value="0">Choose New Location</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Transporter Rate</label>
                                <input type="text" class="form-control" id="transporterRate" name="newTransportRateAmount">
                                <span id="balanceRatePreview"></span>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">&nbsp; &nbsp;</label><br>
                                <button type="submit" class="btn btn-lg btn-primary" id="updateBalanceException">Update Changes</button>
                            </div>
                        </div>

                    </div>
                    
                    <div class="row ml-3 mr-3 mt-3 hidden" id="bitPaymentHolder">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Actual Balance</label>
                                <input type="text" class="form-control" name="actualBalanceAmount" id="actualBalanceAmount">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Pay Part Payment of:</label>
                                <input type="text" class="form-control" name="balancePartPayment" id="balancePartPayment">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Outstanding Balance</label>
                                <input type="text" class="form-control" name="outstandingBalance" id="outstandingBalance">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-semibold">&nbsp; &nbsp;</label>
                                <button type="submit" class="btn btn-primary" id="updateBalanceRequest">Update Changes</button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="balanceExceptionChecker" id="balanceExceptionChecker" value="1">


                </div>

                
            </div>
        </div>  
    </form>
</div>

<!-- Outstanding payment modal -->
<div id="outstandingPaymentModal" class="modal fade">
    <form method="POST" name="frmOutstandingBalance" id="frmOutstandingBalance">
        @csrf
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Outstanding Balance for TRIP: <span id="outstandBalanceTripHolder"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                

                <input type="hidden" name="outstandingBalanceTripId" id="outstandingBalanceTripId">
                <input type="hidden" name="outstandingBalanceId" id="outstandingBalanceId" >
                
                <div class="modal-body">
                    <span id="outstandlingBalanceLoader"></span>

                    <div class="ml-4">
                        <input type="checkbox" id="payalloutstanding" >
                         <span class="font-weight-bold text-danger">Pay Part Payment</span>
                    </div>

                    
                    
                    <div class="row ml-3 mr-3 mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Outstanding Balance</label>
                                <input type="text" class="form-control" name="outstandingBalanceUpdate" id="outstandingBalanceUpdate">
                            </div>
                        </div>
                        <div class="col-md-4 hidden partPaymentofOutstanding">
                            <div class="form-group">
                                <label class="font-weight-semibold">Pay Part Payment of:</label>
                                <input type="text" class="form-control" name="outstandingPartPayment" id="outstandingPartPayment">
                            </div>
                        </div>
                        <div class="col-md-4 hidden partPaymentofOutstanding">
                            <div class="form-group">
                                <label class="font-weight-semibold">Outstanding Balance</label>
                                <input type="text" class="form-control" name="newOutstanding" id="newOutstanding">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-semibold">&nbsp; &nbsp;</label>
                                <button type="submit" class="btn btn-primary" id="updateOutstandingBalanceRequest">Update Changes</button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="outstandBalanceChecker" id="outstandBalanceChecker" value="1">


                </div>

                
            </div>
        </div>  
    </form>
</div>




@stop

@section('script')
<script src="{{URL::asset('js/validator/bulkpayment.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $lastSelectedMemory = localStorage.getItem('name')
        if($lastSelectedMemory == 'advanceLog') {
            $('#balancecarrier').addClass('d-none');
            $('#advancecarrier').removeClass('d-none')
            $('#advanceRequestLog').addClass('text-primary')
            $('#balanceRequestLog').removeClass('text-primary')
            localStorage.setItem('name', 'advanceLog')
        }
        else {
            if($lastSelectedMemory == 'balanceLog') {
                $('#balanceRequestLog').removeClass('text-primary')
                $('#advancecarrier').addClass('d-none');
                $('#balancecarrier').removeClass('d-none')
                $('#balanceRequestLog').addClass('text-primary')
                $('#advanceRequestLog').removeClass('text-primary')
            }
        }
        
        $('.updateClientRate').dblclick(function() {
            $id = $(this).attr("id");
            $(this).addClass("hidden").removeClass("d-block")
            $('#clientRate'+$id).removeClass('hidden')
            $('#approveAdvancePayment').addClass('hidden')
        });

        $('.updateClientRateVal').click(function($e) {
            $e.preventDefault();
            $id = $(this).attr('id');
            $clientRate = $('#clientRateValue'+$id).val();
            $('#gtvtrLoader').html('<i class="icon-spinner2 spinner ml-1"></i>Please wait...')
            $.get('/update-client-rate/'+$id, {client_rate: eval($clientRate)}, function(data) {
                if(data == 'updated') {
                    $('#gtvtrLoader').html('<i class="icon-checkmark2 ml-1"></i>Updated')
                    // window.location.href="";
                }
            })
        })

        $('.updateTransporterRate').dblclick(function() {
            $id = $(this).attr("id");
            $(this).addClass("hidden").removeClass("d-block")
            $('#transporterRate'+$id).removeClass('hidden')
            $('#approveAdvancePayment').addClass('hidden')
        });

        $('.updateTransporterRateVal').click(function($e) {
            $e.preventDefault();
            $id = $(this).attr('id');
            $transporterRate = $('#transporterRateValue'+$id).val();
            $('#trLoader').html('<i class="icon-spinner2 spinner ml-1"></i>')
            $.get('/finance-update-transporter-rate/'+$id, {transporter_rate: eval($transporterRate)}, function(data) {
                if(data == 'updated') {
                    $('#trLoader').html('<i class="icon-checkmark2 ml-1"></i>Updated')
                    $('#trLoader').html('<i class="icon-checkmark2 ml-1"></i>')

                }
            })
        })


        // trForBalanceDefault trForBalanceEditable
        $('.trForBalanceDefault').dblclick(function() {
            $(this).addClass('hidden')
            $('#trForBalanceEditable'+ $(this).attr('id')).removeClass('hidden')
        })

        $('.changeTransporterAtBalance').keypress(function($e) {        
            $oldValue = $(this).attr('value');
            if($e.keyCode === 13) {
                $newValue = eval($(this).val())
                $paymentId = $(this).attr('title')
                if($oldValue === $newValue) {
                    $url = '/payment-request'
                    window.location = $url
                    $e.preventDefault();
                }
                else {
                    $.get('/update-balance-payment', { payment_id: $paymentId, transporter_rate: $newValue }, function(data) {
                        if(data === 'updated') {
                            $url = '/payment-request'
                            window.location = $url
                        }
                        else {
                            return false
                        }
                    })
                }
            }
            
        })

        $('#viewOutstandingLog').click(function() {
            $('#outstandingLogTable').removeClass('hidden')
            $('#collapseOutstandingLog').removeClass('hidden')
            $(this).addClass('hidden');
        })

        $('#collapseOutstandingLog').click(function() {
            $('#outstandingLogTable').addClass('hidden')
            $('#viewOutstandingLog').removeClass('hidden')
            $(this).addClass('hidden');
        })

        $("#advanceRequestLog").click(function() {
            $('#balancecarrier').addClass('d-none');
            $('#advancecarrier').removeClass('d-none')
            $(this).addClass('text-primary')
            $('#balanceRequestLog').removeClass('text-primary')
            localStorage.setItem('name', 'advanceLog')
        })

        $("#balanceRequestLog").click(function() {
            $('#balanceRequestLog').removeClass('text-primary')
            $('#advancecarrier').addClass('d-none');
            $('#balancecarrier').removeClass('d-none')
            $(this).addClass('text-primary')
            $('#advanceRequestLog').removeClass('text-primary')
            localStorage.setItem('name', 'balanceLog')
        })


        $('.multipleSelector').click(function() {
            $('#approveAdvancePayment').addClass('d-none')
            $value = $(this).attr("value")
            if($value == 1) {
                $('#bulkFullPayment').removeClass('d-none')
                $('#multipleZeroAdvance').addClass('d-none')
            }
            else{
                if($value == 2) {
                    $('#bulkFullPayment').addClass('d-none')
                    $('#multipleZeroAdvance').removeClass('d-none')
                }
            }
        })


        $('#bulkFullPayment').click(function($e) {
            $e.preventDefault();
            $atLeastOneIsChecked = $('input:checkbox').is(':checked')
            if(!$atLeastOneIsChecked) {
                alert('Trip check is required.')
                return false
            }
            $('#frmPayment').submit(); 
        })

        $('#multipleZeroAdvance').click(function($e) {
            $e.preventDefault();
            $atLeastOneIsChecked = $('input:checkbox').is(':checked')
            if(!$atLeastOneIsChecked) {
                alert('Trip check is required.')
                return false
            }
            $event = $(this);
            $(this).html('<i class="icon-spinner3 spinner"></i> Processing...').attr('disabled', true);
            $.post('/update-selected-zero-payment', $('#frmPayment').serialize(), function(data) {
                if(data == "updated") {
                    $event.html('<i class="icon-checkmark2"></i> Successfully Updated')
                    window.location.href = '';
                }
                else{
                    $event.html('Error! Operation Aborted')
                }
            })
        })
    })
</script>
@stop