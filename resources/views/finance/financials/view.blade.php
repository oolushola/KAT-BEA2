@extends('layout')

@section('title') Kaya ::. Financials | for Finance @stop

@section('css')
<style type="text/css">
th, td{
    white-space: nowrap;
    margin:0;
}

li {
    font-size: 11px;
    margin-bottom: 3px;
    font-weight: bold
}
td:first-child, .headcol {
    position: sticky;
    left: 0px;
    font-weight: bold;
    background-color: #fbfbfb;
}

.form-control {
    font-size:12px;
    font-weight:bold;
    margin:0px
}
.form-group label {
    margin-bottom: 0px
}
</style>
@stop

@section('main')

@include('orders._helpers')


<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title font-weight-bold">
            <a href="{{URL('financials/dashboard')}}">Financials</a> 
            <i class="icon-cloud-download2 text-info" style="font-size:22px; cursor:pointer" title="Download" id="downloadFinancial"></i>
            <span class="ml-2 text-primary" style="font-size:13px; cursor:pointer" id="showFilter">Show Filter Options</span>
            <span class="ml-2 text-danger hidden" style="font-size:13px; cursor:pointer" id="closeFilter">Close Filter Options</span>
        </h5>
        <ul class="pagination pagination-flat">
            {{ $pagination->links() }}
        </ul>
    </div>

    <div class="row hidden" id="filterFinanceCriteria">
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Clients</label>
            <select class="form-control" name="client" id="clients">
                <optgroup label="KAYA CLIENTS">
                    <option value=""></option>
                    @foreach($clients as $client)
                    <option value="{{$client->id}}">{{ strtoupper($client->company_name) }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Loading Sites</label>
            <div id="loadingSitePlaceHolder">
                <select class="form-control" id="loadingSite">
                    <option value=""></option>
                </select>
            </div>
        </div>
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Invoice Status</label>
            <select class="form-control" id="invoiceStatus">
                <option value=""></option>
                <option value="0">Not Invoiced</option>
                <option value="1">Invoiced</option>
            </select>
        </div>
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Invoice No</label>
            <select class="form-control" id="invoiceNumber">
                <option value=""></option>
                @foreach($invoiceNos as $invoiceNo)
                <option value="{{$invoiceNo->invoice_no}}">{{ ucwords($invoiceNo->completed_invoice_no) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Exact Destination</label>
            <select class="form-control" id="destination">
                <option value=""></option>
                @foreach($destinations as $destination)
                <option value="{{$destination->destination}}">{{ ucwords($destination->destination) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Date From</label>
            <input type="date" id="dateFrom" class="form-control" />
        </div>
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Date To</label>
            <input type="date" id="dateTo" class="form-control" />
        </div>
        <div class="col-md-2 mb-2 ml-1 form-group">
            <label>Payment Status</label>
            <select class="form-control" id="paymentStatus">
                <option value=""></option>
                <option value="1">Paid</option>
                <option value="0">Not Paid</option>
            </select>
        </div>

        <span class="d-block mt-3">
            <button class="btn btn-primary mb-2 ml-2" id="filterFinance">Filter </button>
            <button type="reset" class="btn btn-danger mb-2 ml-2" id="clearFields">Clear </button>
        </span>
        
    </div>
    
    <input type="hidden" name="page" value="">

    <div class="table-responsive" id="contentPlaceholder">
        <table class="table table-bordered" id="exportTableData">
            <thead class="table-info">
                <tr>
                    <th></th>
                    <th id="totalClientRate" class="bg-primary-400"></th>
                    <th id="totalTransporterRate" class="bg-primary-400"></th>
                    <th id="totalGrossMargin" class="bg-primary-400"></th>
                    <th id="averagePercentageMarkup" class="bg-primary-400"></th>
                    <th id="averagePercentageMargin" class="bg-primary-400"></th>
                    <th id="totalAdvancePaid"></th>
                    <th id="totalBalancePaid"></th>
                    <th id="totalAmountPaid"></th>
                    <th colspan="15"></th>
                </tr>
                <tr class="font-weigth-semibold" style="font-size:11px; background:#000; color:#eee; ">
                    <th class="text-center headcol">KAID</th>
                    <th>CLIENT RATE</th>
                    <th>TRANSPORTER RATE</th>
                    <th>GROSS MARGIN</th>
                    <th>% MARKUP</th>
                    <th>% MARGIN</th>
                    <th class="text-center bg-warning-400">ADVANCE</th>
                    <th class="text-center bg-warning-400">BALANCE</th>
                    <th class="text-center text-center bg-warning-400">TOTAL</th>
                    <th class="bg-warning-400">REMARK</th>
                    <th>INVOICE STATUS</th>
                    <th>DATE INVOICE</th>
                    <th class="text-center text-center">INVOICE NO</th>
                    <th class="text-center text-center">BILLED TO</th>
                    <th>DATE PAID</th>
                    <th class="text-center">WAYBILL DETAILS</th>
                    <th>CUSTOMER</th>
                    <th>CURRENT STAGE</th>
                    <th>WAYBILL STATUS</th>
                    <th>TRANSPORTER</th>
                </tr>
            </thead>
            <tbody id="masterDataTable">
                <?php 
                    $counter = 0;
                    $gatedOutCounter = 0;
                    $totalClientRate = 0;
                    $totalTransporterRate = 0;
                    $totalGrossMargin = 0;
                    $averagePercentageMarkup = 0.0;
                    $averagePercentageMargin = 0.0;
                    $totalAdvancePaid = 0;
                    $totalBalancePaid = 0;
                    $totalAmountPaid = 0;
                ?>
                @if(count($pagination))
                    @foreach($pagination as $trip)
                    <?php $counter++;
                    $counter % 2 == 0 ? $css = ' font-weight-semibold ' : $css = 'order-table font-weight-semibold';
                        if($trip->tracker == 1){ $current_stage = 'GATED IN';}
                        if($trip->tracker == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                        if($trip->tracker == 3){ $current_stage = 'LOADING';}
                        if($trip->tracker == 4){ $current_stage = 'DEPARTURE';}
                        if($trip->tracker == 5){ $current_stage = 'GATED OUT';}
                        if($trip->tracker == 6){ $current_stage = 'ON JOURNEY';}
                        if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                        if($trip->tracker == 8){ $current_stage = 'OFFLOADED';}
                    
                    ?>
                        
                    <tr class="{{$css}} hover" style="font-size:10px;" >
                        <td class="text-center">
                            {{$trip->trip_id}}
                            <span class="d-block text-danger">{{strtoupper($trip->truck_no)}}</span>
                            <span class="d-block text-primary">{{strtoupper($trip->loading_site)}}</span>
                        </td>
                        <td class="text-center font-weight-semibold clientRate" value="{{$trip->trip_id}}">
                            <span id="defaultClientRate{{$trip->trip_id}}">{!! number_format($trip->client_rate, 2) !!}</span>
                            <span id="changeClientRate{{$trip->trip_id}}" class="hidden">
                                <input type="text" class="updateClientRate" value="{{$trip->client_rate}}" title="{{$trip->trip_id}}" id="clientRate{{$trip->trip_id}}Value" />
                            </span>
                            <span id="clientRate{{$trip->trip_id}}Loader"></span>
                        </td>

                        <td class="text-center font-weight-semibold transporterRate" value="{{$trip->trip_id}}">
                            <span id="defaultTransporterRate{{$trip->trip_id}}">{!! number_format($trip->transporter_rate, 2) !!}</span>
                            <span id="changeTransporterRate{{$trip->trip_id}}" class="hidden">
                                <input type="text" class="updateTransporterRate" value="{{$trip->transporter_rate}}" title="{{$trip->trip_id}}" id="transporterRate{{$trip->trip_id}}Value" />
                            </span>
                            <span id="transporterRate{{$trip->trip_id}}Loader"></span>
                        </td>

                        <td class="text-center font-weight-semibold">
                            {!! number_format($trip->client_rate - $trip->transporter_rate, 2) !!}
                        </td>
                        <td class="text-center font-weight-semibold">
                            <?php
                                if(isset($trip->transporter_rate) && $trip->transporter_rate != 0){
                                $grossMargin = $trip->client_rate - $trip->transporter_rate;
                                $percentageMarkUp = ($grossMargin / $trip->transporter_rate) * 100;
                                } else {
                                $percentageMarkUp = 0;
                                }
                            ?>
                            {!! number_format($percentageMarkUp, 2) !!}% 
                        </td>

                        <td class="text-center font-weight-semibold"> 
                            <?php 
                                if(isset($trip->client_rate) && $trip->client_rate != 0){
                                    $grossMargin = $trip->client_rate - $trip->transporter_rate;
                                    $percentageMargin = ($grossMargin / $trip->client_rate) * 100;
                                } else {
                                    $percentageMargin = 0;
                                }
                            ?>
                            {!! number_format($percentageMargin, 2) !!}%
                        </td>

                        <td class="text-center font-weight-semibold">
                            <?php
                                $advance = trippayment($trippayments, $trip, 'advance', 'advance_paid');
                                if(isset($advance)) { echo '&#x20a6;'.number_format($advance, 2); } else { echo '';}
                            ?>
                            
                        </td>
                        <td class="text-center font-weight-semibold">
                            <?php
                                $balance = trippayment($trippayments, $trip, 'balance', 'balance_paid');
                                if(isset($balance)) { echo '&#x20a6;'.number_format($balance, 2); } else { echo ''; }
                            ?>
                        </td>
                        <td class="text-center font-weight-semibold">
                            {!! totalPayout($trippayments, $trip, 'advance', 'balance') !!}
                        </td>
                        <td class="font-weight-semibold">
                            {!! exceptionRemarks($trippayments, $trip, 'remark') !!}
                        </td>
                        <td class="text-center">
                            @if($trip->invoice_status)
                                <span class="badge badge-primary">INVOICED</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @foreach($completedInvoices as $invoice)
                                @if($invoice->trip_id == $trip->id)
                                    {{ date('d-m-Y', strtotime($invoice->created_at)) }}
                                @endif
                            @endforeach
                        </td>
                        <td class="text-center">
                            @foreach($completedInvoices as $invoice)
                                @if($invoice->trip_id == $trip->id)
                                    {{ $invoice->invoice_no }}
                                @endif
                            @endforeach
                        </td>
                        <td>
                                @foreach($billers as $biller)
                                    @if($biller->trip_id === $trip->id)
                                        {{ $biller->client_name }}
                                    @endif
                                @endforeach
                        </td>
                        <td class="text-center">
                            @foreach($completedInvoices as $invoice)
                                @if($invoice->trip_id == $trip->id && $invoice->date_paid)
                                    {{ date('d-m-Y', strtotime($invoice->date_paid)) }}
                                @endif
                            @endforeach
                        </td>
                        
                        <td class="text-center font-weight-semibold">
                            @foreach($waybillListings as $waybill)
                                @if($waybill->trip_id == $trip->id)
                                    <span class="d-block">{{ $waybill->sales_order_no }} {{ $waybill->invoice_no }}</span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            {{strtoupper($trip->customers_name)}}
                            <span class="d-block">Destination: {{strtoupper($trip->exact_location_id)}}</span>
                            <span class="d-block">Product: {{strtoupper($trip->product)}}</span>
                        </td>
                        <td class="font-weight-semibold">{{$current_stage}}</td>
                        
                        <td class="text-center">
                            @if($trip->waybill_status == 0)
                                {{ $trip->comment }}
                            @else
                                <i class="icon icon-checkmark2"></i>
                            @endif
                        </td>
                        <td>{{strtoupper($trip->transporter_name)}}</td>
                    </tr>

                    <?php
                        if($trip->tracker >= 5){
                            $gatedOutCounter += 1;
                            if($trip->transporter_rate != 0) {
                                $percentageMarkUp = ($grossMargin / $trip->transporter_rate) * 100;
                            } else {
                                $percentageMarkUp = 0;
                            }

                            $gatedOutCounter+=1;
                            

                            if($trip->client_rate != 0){
                                $percentageMargin = ($grossMargin / $trip->client_rate) * 100;
                            } else {
                                $percentageMargin = 0;
                            }
                        }
                    ?>
                    @endforeach
                @else   
                    <tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>
                @endif  

                <?php
                    $totalClientRate = $revenue[0]->totalRevenue;
                    $totalTransporterRate = $revenue[0]->totalTransporterRate;
                    $totalGrossMargin = $totalClientRate - $totalTransporterRate;
                    $averagePercentageMarkup = $totalGrossMargin / $totalTransporterRate * 100;
                    $averagePercentageMargin = $totalGrossMargin / $totalClientRate * 100;
                    $totalAdvancePaid = $transporterPayment[0]->totaladvancepaid;
                    $totalBalancePaid = $transporterPayment[0]->totalbalancepaid;
                ?>
                
                <input type="hidden" id="clientRate" value="{{number_format($totalClientRate,2)}}">
                <input type="hidden" id="transporterRate" value="{{number_format($totalTransporterRate,2)}}">
                <input type="hidden" id="grossMargin" value="{{number_format($totalGrossMargin,2)}}">
                <input type="hidden" id="percentageMarkup" value="{{number_format($averagePercentageMarkup,2)}}">
                <input type="hidden" id="percentageMargin" value="{{number_format($averagePercentageMargin,2)}}">

                <input type="hidden" id="advancePaid" value="{{number_format($totalAdvancePaid,2)}}">
                <input type="hidden" id="balancePaid" value="{{number_format($totalBalancePaid,2)}}">
                <input type="hidden" id="totalAmount" value="{{number_format($totalBalancePaid + $totalAdvancePaid,2)}}">
            </tbody>
            
        </table>
    </div>
</div>


@stop


@section('script')
<script src="{{URL::asset('js/validator/financials.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('/js/validator/excelme.js')}}"></script>

@stop