@extends('layout')

@section('title')Kaya Pay ::. Payment Breakdown @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Kaya Pay</span> - Payment Breakdown</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>Payment Breakdown</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Update Payment Status</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif Payment Breakdown</h5>
                <i class="icon-stack text-danger-600" id="bulkUpload"></i>
                <i class="icon-stack text-primary-600" id="singleUpload" style="display:none"></i>
            </div>

            <div class="card-body">
                <form id="frmBulkPaymentBreakdown" enctype="multipart/form-data" method="post" action="{{URL('kaya-pay/bulkpayment-breakdown')}}">
                    @csrf
                    <div id="bulkUploadForm" style="display:none">
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="uploadBulkPayment" title="Upload only CSV File"  />

                        </div>
                        <span id="loader1"></span>
                        <button type="submit" class="btn btn-primary" id="uploadBulkRating">Upload Bulk Rates 
                            <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>
                </form>

                <form id="frmPaymentBreakdown" method="post">
                    @csrf
                    @if(isset($recid))
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                    {!! method_field('PATCH') !!}
                    @endif

                    

                    <div id="singleEntryForm">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Client *</label>
                                <select class="form-control" name="client_id" id="clientId">
                                    <option value="0">Choose Client</option>
                                    @foreach($clients as $client)
                                    @if(isset($recid) && $recid->client_id == $client->id)
                                    <option value="{{$client->id}}" selected>{{ $client->company_name }}</option>
                                    @else
                                    <option value="{{$client->id}}">{{ $client->company_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Loading Site *</label>
                                <input type="text" class="form-control" name="loading_site" id="loadingSite" value="<?php if(isset($recid)) { echo $recid->loading_site;} ?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Gate In (Date) *</label>
                                <input type="date" class="form-control" name="gated_in" id="gatedIn" value="<?php if(isset($recid)) { echo $recid->gated_in; } ?>">
                            </div>
                            <legend class="font-weight-semibold"><i class="icon-stack mr-2"></i>Truck Info</legend>
                            <div class="form-group col-md-4">
                                <label>Truck No *</label>
                                <input type="text" class="form-control" placeholder="Sinotrucks" name="truck_no" id="truckNo" value="<?php if(isset($recid)){ echo $recid->truck_no; }  ?>" >
                            </div>

                            <div class="form-group col-md-4">
                                <label>Driver Name</label>
                                <input type="text" class="form-control" name="driver_name" id="driverName" value="<?php if(isset($recid)) { echo $recid->driver_name;} ?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Driver Phone No</label>
                                <input type="number" class="form-control" name="driver_phone_no" id="phoneNumber" value="<?php if(isset($recid)) { echo $recid->driver_phone_no; } ?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Motor Boy Name</label>
                                <input type="text" class="form-control" name="motor_boy_name" id="motorBoyName" value="<?php if(isset($recid)){ echo $recid->motor_boy_name; }  ?>" >
                            </div>

                            <div class="form-group col-md-4">
                                <label>Motor Boy No</label>
                                <input type="text" class="form-control" name="motor_boy_phone_no" id="motorBoyNo" value="<?php if(isset($recid)) { echo $recid->motor_boy_phone_no;} ?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Transporter Name</label>
                                <input type="text" class="form-control" name="transporter_name" id="transporterName" value="<?php if(isset($recid)) { echo $recid->transporter_name; } ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Transporter Phone No.</label>
                                <input type="text" class="form-control" name="transporter_phone_no" id="transporterPhoneNo" value="<?php if(isset($recid)){ echo $recid->transporter_phone_no; }  ?>" >
                            </div>

                            <div class="form-group col-md-4">
                                <label>Destination State *</label>
                                <select class="form-control" name="destination_state" id="destinationState">
                                    <option value="">Choose Destination State</option>
                                    @foreach($states as $state)
                                    @if(isset($recid) && strtolower(trim($state->state)) == strtolower(trim($recid->destination_state)))
                                    <option value="{{$state->state}}" selected>{{$state->state}}</option>
                                    @else
                                    <option value="{{$state->state}}">{{$state->state}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Destination City *</label>
                                <input type="text" class="form-control" name="destination_city" id="destinationCity" value="<?php if(isset($recid)) { echo $recid->destination_city; } ?>">
                            </div>

                            <legend class="font-weight-semibold"><i class="icon-stack mr-2"></i>Date of Event</legend>
                            <div class="form-group col-md-4">
                                <label>At Loading Bay *</label>
                                <input type="date" class="form-control" name="at_loading_bay" id="atLoadingBay" value="<?php if(isset($recid)) { echo $recid->at_loading_bay; } ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Gated Out</label>
                                <input type="date" class="form-control" name="gated_out" id="gatedOut" value="<?php if(isset($recid)) { echo $recid->gated_out; } ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Date of Disbursement *</label>
                                <input type="date" class="form-control" name="payment_disbursed" id="paymentDisbursed" value="<?php if(isset($recid)) { echo $recid->payment_disbursed; } ?>">
                            </div>
                            <legend class="font-weight-semibold"><i class="icon-stack mr-2"></i>Gate Out Details</legend>
                            <div class="form-group col-md-4">
                                <label>Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" id="customerName" value="<?php if(isset($recid)) { echo $recid->customer_name; } ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Customer Phone No.</label>
                                <input type="text" class="form-control" name="customer_phone_no" id="customerPhoneNo" value="<?php if(isset($recid)){ echo $recid->customer_phone_no; }  ?>" >
                            </div>

                            <div class="form-group col-md-4">
                                <label>Waybill No. *</label>
                                <input type="text" class="form-control" name="waybill_no" id="waybillNo" value="<?php if(isset($recid)) { echo $recid->waybill_no;} ?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label>loaded Weight *</label>
                                <input type="text" class="form-control" name="loaded_weight" id="loadedWeight" value="<?php if(isset($recid)) { echo $recid->loaded_weight; } ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Payout Rate *</label>
                                <input type="number" class="form-control" name="finance_cost" id="payoutRate" value="<?php if(isset($recid)) { echo $recid->finance_cost; } ?>">
                            </div>
                        </div>

                        <div class="text-right">
                            <span id="loader"></span>
                            @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updatePaymentBreakdown">Update Payment Breakdown
                            @else
                            <button type="submit" class="btn btn-primary" id="addPaymentBreakdown">Save Payment Breakdown
                            @endif
                                <i class="icon-paperplane ml-2"></i>
                            </button>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
        <!-- /basic layout -->

    </div>

    <div class="col-md-6">
        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Preview Payment Breakdown</h5>
            </div>

            <div class="table-responsive" style="max-height:825px;">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:12px;">
                            <th>Kaya Pay ID</th>
                            <th>Client Info</th>
                            <th>Disbursed</th>
                            <th>Valid Until</th>
                            <th>Waybill No</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($paymentBreakdownListings))
                        @foreach($paymentBreakdownListings as $paymentBreakdown)
                    <?php 
                        $counter++;
                        $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                    ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$paymentBreakdown->kaya_pay_id}}</td>
                                <td>
                                    <span class="font-weight-semibold">{{$paymentBreakdown->client}}, {{ $paymentBreakdown->loading_site }}</span><br />
                                    <span class="badge badge-primary">{{strtoupper($paymentBreakdown->destination_state)}}, {{ strtoupper($paymentBreakdown->destination_city)}}</span>
                                </td>
                                <td>{{ date('d-m-Y', strtotime($paymentBreakdown->payment_disbursed)) }}</td>
                                <td>{{ date('d-m-Y', strtotime($paymentBreakdown->valid_until)) }}</td>
                                <td>{{ strtoupper($paymentBreakdown->waybill_no) }}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('kaya-pay/payment-breakdown/'.$paymentBreakdown->id.'/edit')}}" class="list-icons-item text-primary-600">
                                            <i class="icon-pencil7"></i>
                                        </a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="table-success">You've not added any payment breakdown for kaya pay.</td>
                        </tr>
                    @endif
                        
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /contextual classes -->  
    </div> 
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/jquery.form.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/kaya-pay-paymentBreakdown.js?v=').time()}}"></script>
@stop