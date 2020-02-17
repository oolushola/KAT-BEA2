@extends('layout')

@section('title')Payment Request @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
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

<form method="POST" name="frmPayment" id="frmPayment">
    @csrf
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Payment Request</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <!-- <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <button class="btn btn-success font-weight-semibold" disabled>
                    <i class="icon-coins ml-2"></i><br>₦294,000.00<br>Total Advance Payment Request
                </button>&nbsp;
                        
                <button class="btn btn-primary font-weight-semibold" disabled>
                    <i class="icon-coins ml-2"></i><br>₦126,000.00<br>Total Balance Payment Request 
                </button>                  
            </div>
        </div> -->
    </div>
</div>

<div class="row">
    <div class="col-md-12">
    &nbsp;

        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title font-weight-semibold">Advance Payment Request Log</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info font-weight-semibold">
                        <tr style="font-size:10px;">
                            <th>KAID</th>
                            <th>INVOICE NO.</th>
                            <th>S.O. NUMBER</th>
                            <th>DESTINATION</th>
                            <th>TRANSPORTER</th>
                            <th>TRUCK NO.</th>
                            <th>PRODUCT</th>
                            <th>AMOUNT</th>
                            <th>ACTION</th>
                            <th class="text-center">PAID?</th>
                        </tr>
                    </thead>
                        
                    <tbody>
                        <?php $counter = 0; ?>
                        @if(count($allpendingadvanceRequests))
                            @foreach($allpendingadvanceRequests as $key => $advancePayment)
                            <?php
                                $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                            ?>
                                <tr class="{{$css}} font-weight-semibold" style="font-size:10px;">
                                    <td>{{$advancePayment->trip_id}}</td>
                                    <td>{{getFieldValue($waybillInfos, $advancePayment, 'invoice_no')}}</td>
                                    <td>{{getFieldValue($waybillInfos, $advancePayment, 'sales_order_no')}}</td>
                                    <td>{{$advancePayment->exact_location_id}}, {{$advancePayment->state}}</td>
                                    <td>{{$advancePayment->transporter_name}}</td>
                                    <td>{{$advancePayment->truck_no}}</td>
                                    <td>{{$advancePayment->product}}</td>
                                    <td>
                                        &#x20a6;{{number_format($advancePayment->advance, 2)}}
                                        {!! getPaymentInitiator($chunkPayments, $advancePayment) !!}

                                    </td>
                                    <td>
                                        <span class="badge bg-blue-400 pointer initiatePayment align-right ml-auto" value="{{$advancePayment->id}}" name="">Initiate</span>
                                        
                                        <a href="#advancePaymentExceptionModal" class="badge except bg-warning pointer align-right ml-auto" role="{{$advancePayment->trip_id}}" value="{{$advancePayment->amount}}" id="{{$advancePayment->id}}" data-toggle="modal">Exception</a>
                                        
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" value="{{$advancePayment->id}}" name="approveAdvance[]">
                                    </td>
                                </tr>
                            @endforeach
                                <tr class="table-info">
                                    <td colspan="8"><span class="float-right" id="advanceLoader"></span></td>
                                    <td></td>
                                    <td class="text-center">
                                        <button class="btn btn-primary" id="approveAdvancePayment">Yes!</button>
                                    </td>
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

<div class="row">

    <div class="col-md-12">
    &nbsp;

        <div class="card">
            <div class="card-header header-elements-inline">
                <h6 class="card-title font-weight-semibold">Balance Payment Request Log </h6>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:10px;">
                            <th>KAID</th>
                            <th>INVOICE NO.</th>
                            <th>S.O. NUMBER</th>
                            <th>DESTINATION</th>
                            <th>TRANSPORTER</th>
                            <th>TRUCK NO.</th>
                            <th>PRODUCT</th>
                            <th>AMOUNT</th>
                            <th>ACTION</th>
                            <th>PAID?</th>
                        </tr>
                    </thead>

                    <tbody>
                        
                        <?php $counter = 0; ?>
                        @if(count($allpendingbalanceRequests))
                            @foreach($allpendingbalanceRequests as $key => $balancePayment)
                            <?php
                                $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';

                            ?>
                                <tr class="{{$css}} font-weight-semibold" style="font-size:10px;">
                                    <td>{{$balancePayment->trip_id}}</td>
                                    <td>{{getFieldValue($waybillInfos, $balancePayment, 'invoice_no')}}</td>
                                    <td>{{getFieldValue($waybillInfos, $balancePayment, 'sales_order_no')}}</td>
                                    <td>{{$balancePayment->exact_location_id}}, {{$balancePayment->state}}</td>
                                    <td>{{$balancePayment->transporter_name}}</td>
                                    <td>{{$balancePayment->truck_no}}</td>
                                    <td>{{$balancePayment->product}}</td>
                                    <td>
                                        &#x20a6;{{number_format($balancePayment->balance, 2)}}
                                        {!! getPaymentInitiator($chunkPayments, $balancePayment) !!}

                                    </td>
                                    <td>
                                        <a href="#balancePaymentModal" class="badge balanceInitiator bg-blue-400 pointer align-right ml-auto" value="{{$balancePayment->trip_id}}"  id="{{$balancePayment->id}}" data-toggle="modal">Initiate</a>
                                        
                                        <a href="#balancePaymentExceptionModal" class="badge balanceExcept bg-warning pointer align-right ml-auto" role="{{$balancePayment->trip_id}}" value="{{$balancePayment->exact_location_id}},{{$balancePayment->advance}},{{$balancePayment->balance}},{{$balancePayment->transporter_id}},{{$balancePayment->trip_id}}" id="{{$balancePayment->id}}" data-toggle="modal">Exception</a>

                                    </td>
                                    <td>
                                        <input type="checkbox" value="{{$balancePayment->id}}" name="approveBalance[]">
                                    </td>
                                </tr>
                            @endforeach
                                <tr class="table-info">
                                    <td colspan="9"><span class="float-right" id="balanceLoader"></span></td>
                                    <td>
                                        <button class="btn btn-primary" id="approveBalancePayment">Pay</button>
                                    </td>
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
                    <ul style="margin:0; padding:0;">
                        <li class="exception exception-default" id="percentageDifference">% Difference</li>
                        <li class="exception" id="fullPayment">Full Payment</li>
                        <input type="text" value="2" name="advance_exception" id="advanceNavigator">
                    </ul>

                    <div class="row ml-3 mr-3 mt-3 show" id="percentHolder">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-semibold">Amount (₦)</label>
                                <input type="text" class="form-control" id="totalAmountHolder" disabled>
                                <input type="text" id="totalAmount_" name="total_amount" value="" >
                                <input type="text" value="" id="payid" name="payid" >
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
                            <input type="text" name="pay_in_full" id="pay_in_full" value="0" >
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
                    <div class="row ml-3 mr-3 mt-3 show" id="percentHolder">
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
                </div>

                
            </div>
        </div>  
    </form>
</div>

<!-- Modal HTML for Initiating Balance Request -->
<div id="balancePaymentModal" class="modal fade">
    @csrf
    <form method="POST" name="frmInitiateBalance" id="frmInitiateBalance">
        @csrf
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Balance<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <input type="hidden" name="tripIdofBalanceInitiate" id="tripIdofBalanceInitiate" >
                <input type="hidden" name="balanceInitiateId" id="balanceInitiateId" >
                <input type="hidden" name="proceed_confirmation" id="confirmProceed" value="">
                
                <div class="modal-body">
                    <div class="row ml-3 mr-3 mt-3 show">
                        <div class="col-md-12" id="waitLoader"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-semibold">&nbsp; &nbsp;</label>
                                <button type="submit" class="btn btn-lg btn-primary" id="proceedBalancePayment">Proceed to Payment</button><br>
                                <section class="hidden" id="proceedAnywayConfirmation">
                                    <input type="checkbox" id="confirmCheck" style="margin-left:5px;" > <span style="font-size:9px;">Confirm to proceed to payment initation</span>
                                    
                                <section>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-semibold">&nbsp; &nbsp;</label>
                                <button type="submit" class="btn btn-lg btn-danger" id="abortOperation">Abort Operation</button>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>  
    </form>
</div>


@stop

@section('script')
<script src="{{URL::asset('js/validator/bulkpayment.js')}}" type="text/javascript"></script>
@stop