@extends('layout')

@section('title') Kaya ::. Financials | for Finance @stop

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

@include('orders._helpers')


<div class="card">
    <span id="responsePlace"></span>
    <div class="card-header header-elements-inline">
        <h5 class="card-title">Financials</h5>
    </div>

    <div class="row mb-2 ml-2 mr-2 hidden" id="yearSortHolder">
        @include('orders._sorters.year-sort')
    </div>
    <div class="row mb-2 ml-2 mr-2 hidden" id="monthSortHolder">
        @include('orders._sorters.month-sort')
    </div>
    <div class="row mb-2 ml-2 mr-2 hidden" id="weeklySortHolder">
        @include('orders._sorters.week-sort')
    </div>

    
    <span>Filter by: Waybill Status
        <select name="waybillStatus" id="waybillStatus">
            <option value="0">All Status</option>
            <option value="1">Healthy</option>
            <option value="2">Warning</option>
            <option value="3">Extreme</option>
        </select>
    </span>   

    <div class="table-responsive" id="contentPlaceholder">
        <table class="table table-bordered">
            <thead class="table-info">
                <tr>
                    <th colspan="10" style="background:#fff"></th>
                    <th id="totalClientRate" class="bg-primary-400"></th>
                    <th id="totalTransporterRate" class="bg-primary-400"></th>
                    <th id="totalGrossMargin" class="bg-primary-400"></th>
                    <th id="averagePercentageMarkup" class="bg-primary-400"></th>
                    <th id="averagePercentageMargin" class="bg-primary-400"></th>
                    <th id="totalAdvancePaid"></th>
                    <th id="totalBalancePaid"></th>
                    <th id="totalAmountPaid"></th>
                </tr>
                <tr class="font-weigth-semibold" style="font-size:11px; background:#000; color:#eee; ">
                    <th class="text-center">KAID</th>
                    <th class="text-center">INVOICE NO.</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th>DESTINATION</th>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO.</th>
                    <th>PRODUCT</th>
                    <th>CUSTOMER</th>
                    <th>CURRENT STAGE</th>
                    <th>INVOICE STATUS</th>
                    <th>CLIENT RATE</th>
                    <th>TRANSPORTER RATE</th>
                    <th>GROSS MARGIN</th>
                    <th>% MARKUP</th>
                    <th>% MARGIN</th>
                    <th class="text-center bg-warning-400">ADVANCE</th>
                    <th class="text-center bg-warning-400">BALANCE</th>
                    <th class="text-center text-center bg-warning-400">TOTAL</th>
                    <th class="bg-warning-400">REMARK</th>
                    <th>WAYBILL STATUS</th>
                    <th>WAYBILL COLLECTION DATE</th>
                    <th>DATE INVOICE</th>
                    <th>DATE PAID</th>
                    <th>WAYBILL INDICATOR</th>
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
                @if(count($orders))
                    @foreach($orders as $trip)
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
                    
                    if($trip->gated_out != '') {
                        if(count($waybillstatuses)){
                            foreach($waybillstatuses as $waybillChecker){
                                if($waybillChecker->trip_id == $trip->id && $waybillChecker->waybill_status == TRUE) {
                                    $bgcolor = '#fff';
                                    $color = '#000';
                                    $textdescription = 'AT HQ';
                                    break;
                                } else {
                                    $now = time();
                                    $gatedout = strtotime($trip->gated_out);;
                                    $datediff = $now - $gatedout;
                                    $numberofdays = abs(round($datediff / (60 * 60 * 24)));
                                    if($numberofdays >=0 && $numberofdays <= 3){
                                        $bgcolor = '#008000';
                                        $textdescription = 'HEALTHY';
                                        $color = '#fff';
                                    }
                                    elseif($numberofdays >=4 && $numberofdays <= 7){
                                        $bgcolor = '#FFBF00';
                                        $textdescription = 'WARNING '.$numberofdays.' Days ';
                                        $color = '#fff';
                                    }
                                    else{
                                        $bgcolor = '#FF0000';
                                        $textdescription = 'EXTREME | '.$numberofdays.' Days ';
                                        $color = '#fff';
                                    }
                                }
                                continue;
                            }
                        }
                        else{
                            $bgcolor = '';
                            $textdescription = 'Waybill Status Not Updated';
                            $color= '#000';
                        }
                    }
                    else{
                        $bgcolor = '';
                        $textdescription = 'Not gated out yet';
                        $color= '#000';
                    }

$trip->arrival_at_loading_bay?$alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
$trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
$trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
$trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
$trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

                    ?>
                        
                    <tr class="{{$css}} hover" style="font-size:10px;" >
                        <td class="text-center">{{$trip->trip_id}}</td>
                        <td class="text-center font-weight-semibold">
                            @foreach($tripWaybills as $invoiceNo)
                                @if($trip->id == $invoiceNo->trip_id)
                                    {{$invoiceNo->invoice_no}}<br>
                                @endif
                            @endforeach
                        </td>
                        <td>{{strtoupper($trip->truck_no)}}</td>
                        <td>{{strtoupper($trip->exact_location_id)}}</td>
                        <td>{{$trip->loading_site}}</td>
                        <td class="text-center font-weight-semibold">
                            @foreach($tripWaybills as $salesNo)
                                @if($trip->id == $salesNo->trip_id)
                                <a href="{{URL::asset('assets/img/waybills/'.$salesNo->photo)}}" target="_blank" title="View waybill {{$salesNo->sales_order_no}}">
                                {{$salesNo->sales_order_no}}<br>
                                </a>
                                @endif
                            @endforeach
                        </td>
                        <td>{{$trip->product}}</td>
                        <td>{{strtoupper($trip->customers_name)}}</td>
                        <td class="font-weight-semibold">{{$current_stage}}</td>
                        <td>
                            
                            @foreach($invoiceCriteria as $invoiceStatus)
                                @if($invoiceStatus->trip_id == $trip->id)
                                    <span class="badge badge-primary">INVOICED</span>
                                    @break
                                @endif
                            @endforeach
                        </td>
                        <td class="text-center font-weight-semibold">
                            &#x20a6;{!! number_format($trip->client_rate, 2) !!}
                        </td>
                        <td class="text-center font-weight-semibold">
                            &#x20a6;{!! number_format($trip->transporter_rate, 2)  !!}
                        </td>
                        <td class="text-center font-weight-semibold">
                            &#x20a6;{!! number_format($trip->client_rate - $trip->transporter_rate, 2) !!}
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
                        <td>
                            @foreach($waybillstatuses as $waybillstatus)
                                @if($waybillstatus->trip_id == $trip->id)
                                    {{$waybillstatus->comment}}
                                @endif
                            @endforeach
                        </td>
                        <td class="text-center">
                            @foreach($waybillstatuses as $collectionDate)
                                @if(($collectionDate->trip_id == $trip->id) && ($collectionDate->waybill_status == TRUE))
                                    {{$collectionDate->updated_at}}
                                @endif
                            @endforeach
                        </td>
                        <td class="text-center">
                            @foreach($waybillstatuses as $dateInvoiced)
                                @if(($dateInvoiced->trip_id == $trip->id))
                                    {{$dateInvoiced->date_invoiced}}
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach($invoiceCriteria as $datePaid)
                                @if($datePaid->trip_id == $trip->id && $datePaid->paid_status == TRUE)
                                    {{$datePaid->updated_at}}
                                @endif
                            @endforeach
                        </td>
                        <td class="text-center" style="background:{{$bgcolor}}; color:{{$color}}">{{$textdescription}}</td>                        
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

                            $totalAdvancePaid += trippayment($trippayments, $trip, 'advance', 'advance_paid');
                            $totalBalancePaid += trippayment($trippayments, $trip, 'balance', 'balance_paid');

                        }
                    ?>
                    @endforeach
                @else   
                    <tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>
                @endif  

                <?php
                    $totalClientRate = $totalRevenue[0]->totalRevenue;
                    $totalTransporterRate = $transporterRate[0]->totalTransporterRate;
                    $totalGrossMargin = $totalClientRate - $totalTransporterRate;
                    $averagePercentageMarkup = $totalGrossMargin / $totalTransporterRate * 100;
                    $averagePercentageMargin = $totalGrossMargin / $totalClientRate * 100;
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
<script type="text/javascript">
$(function(){
    $("#totalClientRate").html($('#clientRate').val());
    $("#totalTransporterRate").html($('#transporterRate').val());
    $("#totalGrossMargin").html($('#grossMargin').val());
    $("#averagePercentageMarkup").html('%'+$('#percentageMarkup').val());
    $("#averagePercentageMargin").html('%'+$('#percentageMargin').val());
    $("#totalAdvancePaid").html('&#x20a6;'+$('#advancePaid').val());
    $("#totalBalancePaid").html('&#x20a6;'+$('#balancePaid').val());
    $("#totalAmountPaid").html('&#x20a6;'+$('#totalAmount').val());

})
</script>
@stop