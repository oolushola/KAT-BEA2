@extends('layout')

@section('title') Kaya ::. Not Gated Out Trips @stop

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

    <div class="container">

        <div class="row">
        <div class="col-md-3 col-sm-4 col-5">
            <a href="{{URL('trips')}}"><button class="btn btn-primary font-weight-semibold" style="font-size:10px; border-radius:0; margin-left:-10px;">CREATE NEW ORDER</button></a>
        </div>
        <div class="col-md-3 offset-md-6 col-sm-8 col-7">
            <a href="{{URL('truck-availability-list')}}"><button class="btn btn-warning font-weight-semibold" style="font-size:10px; border-radius:0; margin-left:-10px; margin-right:-10px; color:#000">CREATE TRIP FROM TRUCK AVAILABILITY({{$countAvailableTrucks}})</button></a>
        </div>
        
        </div>


        <h2 style="font-size:18px; padding:5px;" class="font-weight-semibold">Update Existing Trip</sub></h2>
        <p><strong>Note:</strong> The following <strong>trips</strong> are orders that must have gated in but yet to gate. To update, click on the <strong>'Update'</strong> button to proceed with the trip order information</p>
    
    <div class="panel-group" id="accordion">
        <?php $counter = 0; ?>
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

    $trip->arrival_at_loading_bay?$alb = date('Y-m-d, g:i A',strtotime($trip->arrival_at_loading_bay)):$alb = '';
    $trip->loading_start_time ? $lst = date('Y-m-d, g:i A',strtotime($trip->loading_start_time)) : $lst = '';
    $trip->loading_end_time ? $let = date('Y-m-d, g:i A',strtotime($trip->loading_end_time)) : $let = '';
    $trip->departure_date_time ? $ddt = date('Y-m-d, g:i A',strtotime($trip->departure_date_time)) : $ddt = '';
    $trip->gated_out ? $gto = date('Y-m-d, g:i A',strtotime($trip->gated_out)) : $gto = '';

            ?>


        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title font-weight-semibold" style="background:#f5f5f5; padding:5px; border:1px solid #ccc; font-size:13px;">
                    <a data-toggle="collapse" data-parent="#accordion" href="#{{$trip->trip_id}}">{{$trip->trip_id}}</a>

                    <span style="font-size:11px; font-weight:bold">{{$trip->truck_no}} |</span>

                    <span style="font-size:11px; font-weight:bold">{{$current_stage}}</span>

                    <a href="{{URL('/trips/'.$trip->id.'/edit')}}" class="list-icons-item text-primary-600" title="Update this trip"><button type="button" class="btn btn-warning font-weight-semibold" style="font-size:10px;">UPDATE</button></a>
                    <p>{{$trip->loading_site}} | <a href="{{URL('way-bill/'.$trip->trip_id.'/'.$trip->loading_site)}}">Update S.O. & Invoice No.</a></p>
                    

                </h4>
            </div>

            <div id="{{$trip->trip_id}}" class="panel-collapse collapse in" style="padding-bottom:10px; margin-top:-10px;">
                <div class="panel-body table-responsive">
                    <table class="table table-bordered" style="font-size:11px;">
                        <tr>
                            <td class="font-weight-semibold">LOADING SITE</td>
                            <td>{{$trip->loading_site}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">SALES ORDER NO</td>
                            <td>
                                @foreach($tripWaybills as $salesNo)
                                    @if($trip->id == $salesNo->trip_id)
                                    <a href="{{URL::asset('assets/img/waybills/'.$salesNo->photo)}}" target="_blank" title="View waybill {{$salesNo->sales_order_no}}">
                                    {{$salesNo->sales_order_no}} 
                                    </a>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRUCK NO.</td>
                            <td>{{strtoupper($trip->truck_no)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">ACCOUNT OFFICER</td>
                            <td>{{strtoupper($trip->account_officer)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRANSPORTERS NAME</td>
                            <td>{{strtoupper($trip->transporter_name)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRANSPORTER'S NUMBER</td>
                            <td>{{$trip->phone_no}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRUCK TYPE</td>
                            <td>{{strtoupper($trip->truck_type)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TONNAGE<sub>(kg)</sub></td>
                            <td>{{$trip->tonnage}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">DRIVER'S FULLNAME</td>
                            <td>{{strtoupper($trip->driver_first_name)}} {{strtoupper($trip->driver_last_name)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">DRIVER'S PHONE NUMBER</td>
                            <td>{{$trip->driver_phone_number}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">MOTOR BOY'S NAME</td>
                            <td>{{strtoupper($trip->motor_boy_first_name)}} {{strtoupper($trip->motor_boy_last_name)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">MOTOR BOY's NUMBER</td>
                            <td>{{$trip->motor_boy_phone_no}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">INVOICE | WAYBILL NUMBER</td>
                            <td>
                                @foreach($tripWaybills as $invoiceNo)
                                    @if($trip->id == $invoiceNo->trip_id)
                                        {{$invoiceNo->invoice_no}} 
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">CUSTOMER'S NAME</td>
                            <td>{{strtoupper($trip->customers_name)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">CUSTOMER'S NUMBER</td>
                            <td>{{$trip->customer_no}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">CUSTOMER'S ADDRESS</td>
                            <td>{{$trip->customer_address}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">DESTINATION</td>
                            <td>{{strtoupper($trip->exact_location_id)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">WEIGHT</td>
                            <td>{{$trip->loaded_weight}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">PRODUCT</td>
                            <td>{{$trip->product}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">QUANTITY</td>
                            <td>{{$trip->loaded_quantity}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">GATE IN</td>
                            <td>{{date('Y-m-d, g:i A',strtotime($trip->gate_in))}}</td>
                        </tr>

                        <tr>
                            <td class="font-weight-semibold">TIME SINCE GATED IN</td>
                            <td>{{timeDifference($trip->gate_in, $trip->arrival_at_loading_bay)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">ARRIVAL AT LOADING BAY</td>
                            <td>{{$alb}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TIME LOADING STARTED</td>
                            <td>{{$lst}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TIME LOADING END</td>
                            <td>{{$let}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">LOADING BAY DEPARTURE TIME</td>
                            <td>{{$ddt}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">GATED OUT</td>
                            <td>{{$gto}}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        

        @endforeach

        @else

        no trip
        @endif

    </div> 
    </div>

</div>




@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/master-data.js')}}"></script>
@stop