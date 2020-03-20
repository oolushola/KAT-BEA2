@extends('layout')

@section('title') Kaya ::. Trips Currently on Journey @stop

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
        
        @if(Auth::user()->id == 1)
        <form action="{{URL('/upload-bulk-trip')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group d-none d-sm-block">
            <input type="file" name="bulktrip" style="font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size:10px">Upload Bulk Trip</button>
        </div> 
        </form>

        <form action="{{URL('/upload-bulk-sales-order-no')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group d-none d-sm-block">
            <input type="file" name="bulksalesOrder" style="font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size:10px">Upload Bulk Sales Order No</button>
        </div> 
        </form>

        <form action="{{URL('/upload-bulk-event-time')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group d-none d-sm-block">
            <input type="file" name="eventTime" style="font-size:10px">
            <button type="submit" class="btn btn-primary" style="font-size:10px">Upload Bulk Event Time</button>
        </div> 
        </form>

        @endif



        <div>&nbsp; </div>


        <p style="font-size:10px;" class="d-none d-sm-block">Elastic Sort By: 
            <span class="font-weight-semibold">General 
                <input type="radio" name="elasticSearch" class="esearch" value="0" checked> &nbsp; &nbsp;
            </span>
            <span class="font-weight-semibold">Year 
                <input type="radio" name="elasticSearch" class="esearch" value="1"> &nbsp; &nbsp;
            </span>
            <span class="font-weight-semibold">Month 
                <input type="radio" name="elasticSearch" class="esearch" value="2"> &nbsp; &nbsp;
            <span>
            <span class="font-weight-semibold">Week 
                <input type="radio" name="elasticSearch" class="esearch" value="3"> &nbsp; &nbsp;
            </span><br>
            <input type="text" class="form-control mt-1" placeholder="Search Record" id="searchDataset" />
        </p>
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

    <div class="row mb-2 ml-2 mr-2" id="generalSorter">
        <div class="col-md-2 mb-2">
            <select class="form-control" id="clientId">
                <option>Client</option>
                @foreach($clients as $client)
                    <option value="{{$client->id}}">{{ucwords($client->company_name)}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <div id="loadingSitePlaceholder">
                <select class="form-control">
                    <option value="0">Loading Site</option>
                    @foreach($loadingSites as $siteofloading)
                    <option value="{{$siteofloading->id}}">{{$siteofloading->loading_site}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 mb-2">
            <select class="form-control" id="status">
                <option value="0">Status</option>
                <option value="1">Gated In</option>
                <option value="2">Arrived at loading bay</option>
                <option value="3">Loading</option>
                <option value="4">Depated Loading Bay</option>
                <option value="5">Gated Out</option>
                <option value="6">On Journey</option>
                <option value="7">Arrived Destination</option>
                <option value="8">Offloaded</option>
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <select class="form-control" id="waybillstatus">
                <option>Waybill Status</option>
                <option value="1">Received</option>
                <option value="0">Otherwise</option>
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <select class="form-control" id="transporterId">
                <option value="0">Transporter</option>
                @foreach($transporters as $transporter)
                    <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <select class="form-control" id="productId">
                <option value="0">Choose Product</option>
                @foreach($transporters as $transporter)
                    <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
                @endforeach
            </select>
        </div>

        
    </div>
    
    <form method="POST" id="frmCancelTrip">
    @csrf {!! method_field('PATCH') !!}
    <div class="table-responsive" id="contentPlaceholder">
        <table class="table table-bordered">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee;">
                <tr class="font-weigth-semibold">
                    <th class="text-center">KAID</th>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO.</th>
                    <th class="text-center">INVOICE NO.</th>
                    <th class="text-center">TRUCK NO.</th>
                    <th>ACCOUNT OFFICER</th>
                    <th>FIELD OPS</th>
                    <th>TRANSPORTER'S NAME</th>
                    <th>TRANSPORTER'S NUMBER</th>
                    <th>TRUCK TYPE</th>
                    <th>TONNAGE<sub>(Kg)</sub></th>
                    <th>DRIVER</th>
                    <th class="text-center">DRIVER'S No.</th>
                    <th>MOTOR BOY</th>
                    <th class="text-center">MOTOR BOY'S NO.</th>
                    
                    <th>CUSTOMER's NAME</th>
                    <th class="text-center">CUSTOMER'S NO.</th>
                    <th>CUSTOMER's ADDRESS</th>
                    <th>DESTINATION</th>
                    <th>WEIGHT</th>
                    <th>PRODUCT</th>
                    <th>QUANTITY</th>
                    <th class="text-center">GATE IN</th>
                    <th>TIME SINCE GATED IN</th>
                    <th class="text-center">ARRIVAL AT LOADING BAY</th>
                    <th class="text-center">TIME LOADING STARTED</th>
                    <th class="text-center">TIME LOADING ENDED</th>
                    <th class="text-center">LOADING BAY DEPARTURE TIME</th>
                    <th class="text-center">GATED OUT</th>
                    <th>LAST KNOWN LOCATION 1</th>
                    <th>LATEST TIME 1 <sub>(10:00AM)</sub></th>
                    <th>LAST KNOWN LOCATION 2</th>
                    <th>LATEST TIME 2 <sub>(4:00PM)</sub></th>
                    <th>TIME ARRIVED DESTINATION</th>
                    <th class="text-center">OFFLOADING DURATION</th>
                    <!-- <th>DISTANCE <sub>(km)</sub></th> -->
                    <th>CURRENT STAGE</th>
                    <th>WAYBILL STATUS</th>
                    <th>WAYBILL INDICATOR</th>
                    <!-- <th>INVOICE STATUS</th>
                    <th>WAYBILL COLLECTION DATE</th>
                    <th>DATE INVOICE</th>
                    <th>DATE PAID</th>
                    <th>CLIENT RATE</th>
                    <th>TRANSPORTER RATE</th>
                    <th>GROSS MARGIN</th>
                    <th>% MARKUP</th>
                    <th>% MARGIN</th>
                    <th>ADVANCE</th>
                    <th>BALANCE</th>
                    <th>TOTAL</th>
                    <th>REMARK</th>
                    <th>Action</th> -->
                </tr>
            </thead>
            <tbody id="masterDataTable">
                <?php $counter = 0; ?>
                @if(count($onJourneyTrips))
                    @foreach($onJourneyTrips as $trip)
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
                        
                    <tr class="{{$css}} hover" style="font-size:10px;">
                        <td class="text-center" style="">
                            <a href="{{URL('/trips/'.$trip->id.'/edit')}}" class="list-icons-item text-primary-600" title="Update this trip">{{$trip->trip_id}}</a>

                            <a href="{{URL('way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site))}}" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check text-warning-600"></i>
                            </a>
                            
                            <a href="{{URL('trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site))}}" class="list-icons-item text-info-600" title="Add Events">
                                <i class="icon-calendar52 text-success-600"></i>
                            </a>

                            <span class="list-icons-item">
                                @if($trip->tracker < 5)
                                    <i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>
                                @else
                                <i class="icon icon-checkmark2" title="Gated Out"></i>
                                @endif
                            </span>

                        </td>
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
                        <td class="text-center font-weight-semibold">
                            @foreach($tripWaybills as $invoiceNo)
                                <?php 
                                    $kaid = str_replace('KAID', '', $trip->trip_id );
                                ?>
                                @if($trip->id == $invoiceNo->trip_id)
                                    {{$invoiceNo->invoice_no}}<br>
                                @endif
                            @endforeach
                        </td>
                        <td>{{strtoupper($trip->truck_no)}}</td>
                        <td>{{strtoupper($trip->account_officer)}}</th>
                        <td>{{strtoupper($trip->first_name)}} {{strtoupper($trip->last_name)}}</td>
                        <td>{{strtoupper($trip->transporter_name)}}</td>
                        <td class="text-center">{{$trip->phone_no}}</td>
                        <td>{{strtoupper($trip->truck_type)}}</td>
                        <td>{{$trip->tonnage}}</td>
                        <td>{{strtoupper($trip->driver_first_name)}} {{strtoupper($trip->driver_last_name)}}</td>
                        <td class="text-center">{{$trip->driver_phone_number}}</td>
                        <td>{{strtoupper($trip->motor_boy_first_name)}} {{strtoupper($trip->motor_boy_last_name)}}</td>
                        <td class="text-center">{{$trip->motor_boy_phone_no}}</td>
                        
                        <td>{{strtoupper($trip->customers_name)}}</td>
                        <td class="text-center">{{$trip->customer_no}}</td>
                        <td>{{$trip->customer_address}}</td>
                        <td>{{strtoupper($trip->exact_location_id)}}</td>
                        <td>{{$trip->loaded_weight}}</td>
                        <td>{{$trip->product}}</td>
                        <td>{{$trip->loaded_quantity}}</td>
                        <td class="text-center">
                            {{date('Y-m-d, g:i A',strtotime($trip->gate_in))}}
                        </td>
                        <td>
                            {{timeDifference($trip->gate_in, $trip->arrival_at_loading_bay)}}
                        </td>
                        <td class="text-center">{{$alb}}</td>
                        <td class="text-center">{{$lst}}</td>
                        <td class="text-center">{{$let}}</td>
                        <td class="text-center">{{$ddt}}</td>
                        <td class="text-center">{{$gto}}</td>
                        <td>{{eventdetails($tripEvents, $trip, 'location_one_comment')}}</td>
                        <td class="text-center">
                            {{eventdetails($tripEvents, $trip, 'location_check_one')}}
                        </td>
                        <td>{{eventdetails($tripEvents, $trip, 'location_two_comment')}}</td>
                        <td class="text-center">
                            {{eventdetails($tripEvents, $trip, 'location_check_two')}}
                        </td>
                        <td class="text-center">
                            {{eventdetails($tripEvents, $trip, 'time_arrived_destination')}}
                        </td>
                        <td class="text-center">
                            {{eventdetails($tripEvents, $trip, 'offload_start_time')}} - 
                            {{eventdetails($tripEvents, $trip, 'offload_end_time')}}
                        </td>
                        <!-- <td></td> -->
                        <td class="font-weight-semibold">{{$current_stage}}</td>
                        <td>
                            @foreach($waybillstatuses as $waybillstatus)
                                @if($waybillstatus->trip_id == $trip->id)
                                    {{$waybillstatus->comment}}
                                @endif
                            @endforeach
                        </td>
                        <td class="text-center" style="background:{{$bgcolor}}; color:{{$color}}">{{$textdescription}}</td>                        
                        <!-- <td class="text-center">
                            @foreach($invoiceCriteria as $invoiceStatus)
                                @if($invoiceStatus->trip_id == $trip->id && $invoiceStatus->invoice_status == TRUE)
                                    <span class="badge badge-primary">INVOICED</span>
                                    @break
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
                            &#x20a6;{!! number_format(trippayment($trippayments, $trip, 'advance', 'advance_paid'), 2) !!}
                        </td>
                        <td class="text-center font-weight-semibold">
                            &#x20a6;{!! number_format(trippayment($trippayments, $trip, 'balance', 'balance_paid'), 2) !!}
                        </td>
                        <td class="text-center font-weight-semibold">
                            {!! totalPayout($trippayments, $trip, 'advance', 'balance') !!}
                        </td>
                        <td class="font-weight-semibold">
                            {!! exceptionRemarks($trippayments, $trip, 'remark') !!}
                        </td>

                        <td>
                            <div class="list-icons">

                             <a href="{{URL('/trips/'.$trip->id.'/edit')}}" class="list-icons-item text-primary-600" title="Update this trip"><i class="icon-pencil7 text-primary-600"></i>
                            </a>

                                <a href="{{URL('way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site))}}" class="list-icons-item text-warning-600" title="Waybill Status">
                                    <i class="icon-file-check text-warning-600"></i>
                                </a>

                                <a href="{{URL('trip/'.$trip->trip_id.'/'.str_slug($trip->loading_site))}}" class="list-icons-item text-info-600" title="Add Events">
                                    <i class="icon-calendar52 text-success-600"></i>
                                </a>

                                <span class="list-icons-item">
                                    @if($trip->tracker < 5)
                                    
                                        <i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>
                                    
                                    @else
                                    <i class="icon icon-checkmark2" title="Gated Out"></i>
                                    @endif
                                </span>
                            </div>
                        </td> -->
                    </tr>
                    @endforeach
                @else   
                    <tr>
                        <td class="table-success" colspan="30">No pending trip.</td>
                    </tr>
                @endif            

            </tbody>
        </table>
    </div>
    </form>
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/master-data.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/sort.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/elastic-sort.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/trip.js')}}"></script>

<script>
$(window).scroll(function() {
    if($(window).scrollTop() == $(document).height() - $(window).height()) {
           // ajax call get data from server and append to the div
    }
});
</script>
@stop