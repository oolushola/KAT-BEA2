@extends('layout')

@section('title'){{$kayaid}} Detailed Trip Overview @stop
<?php
    //$transporter_rate = $transporterRate[0]->transporter_amount_rate;
    $expected_advance_payment = 0.7 * $transporterRate;
    $expected_balance = 0.3 * $transporterRate;
    $tracker = $orders[0]->tracker;
?>
@section('main')
<form method="POST" name="frmPaymentRequest" id="frmPaymentRequest">
    @csrf
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <i class="icon-arrow-left52 mr-2"></i>
                <span class="font-weight-semibold">Transporter Rate: &#x20a6;{{number_format($transporterRate, 2)}}</span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>

<div class="card-header bg-white header-elements-inline">
    <h6 class="card-title font-weight-semibold">WAYBILL AND INVOICE NO.</h6>
</div>
<table class="table table-bordered">
    <thead class="table-info">
        <tr class="font-weigth-semibold"  style="font-size:10px;">
            <th>SALES ORDER NO.</th>
            <th>INVOICE NO.</th>
            <th>WAYBILL APPROVAL TIMESTAMP</th>
        </tr>
        <tbody>
            @if(count($tripWaybills))
            <?php $counter = 0; ?>
            @foreach($tripWaybills as $tripwaybill)
            <?php
            $counter++;
            $counter % 2 == 0 ? $css = 'table-success font-weight-semibold' : $css = ' font-weight-semibold';
            if($tripwaybill->moment_approved == ''){
                $result = 'The waybill has not been approved.';
            }
            else{
                $result = date('Y-m-d, g:i A', strtotime($tripwaybill->moment_approved));
            }
            ?>
                <tr class="{{$css}}" style="font-size:10px">
                    <td>{{$tripwaybill->sales_order_no}}</td>
                    <td>{{$tripwaybill->invoice_no}}</td>
                    <td>{{$result}}</td>
                </tr>
            @endforeach
            @endif
        </tbody>
</table>

<br>

<div class="row">
    <div class="col-md-4" >
        <div class="card bg-secondary-600 no-border">
            <div class="card-header header-elements-inline">
                <h5 class="card-title info-subheading"></h5>
            </div>
            <div class="card-body">
                <h1 style="color:#000; font-weight:bold; text-align:center; font-size:43px; ">{{$kayaid}}</h1> 
            </div>   
        </div>
    </div>
    <div class="col-md-4" >
        <div class="card bg-primary-600 no-border">
            <div class="card-header header-elements-inline">
                <h5 class="card-title info-subheading">{{$orders[0]->loading_site}}</h5>
            </div>
            <div class="card-body">
                <span class="defined-title">{{$orders[0]->company_name}}</span> 
                <p class="info">
                <span style="text-transform:capitalize">{{$orders[0]->address}}</span>
                </p>
            </div>   
        </div>
    </div>
    <div class="col-md-4 ">
        <div class="card bg-success no-border">
            <div class="card-header header-elements-inline">
                <h5 class="card-title info-subheading" style="padding:0; margin:0">{{$orders[0]->customers_name}}</h5>
            </div>
            <div class="card-body">
                <span class="defined-title">{{$orders[0]->exact_location_id}}</span>
                <p class="info">
                    <span style="text-transform:capitalize">{{$orders[0]->customer_address}}</span>
                </p>
            </div>   
        </div>
    </div>
    <div class="col-md-4 ">
        <div class="card bg-info no-border"">
            <div class="card-header header-elements-inline">
                <h5 class="card-title info-subheading">PRODUCT DETAILS </h5>
            </div>
            <div class="card-body">
                <span class="defined-title">{{$orders[0]->product}}</span> 
                <p class="info">
                <span><sub>Weight: </sub>{{$orders[0]->loaded_weight}} <sub>Quantity:</sub>{{$orders[0]->loaded_quantity}}</span>

                </p>
            </div>   
        </div>
    </div>

    <div class="col-md-4 ">
        <div class="card bg-success no-border">
            <div class="card-header header-elements-inline">
                <h5 class="card-title info  info-subheading">
                    Truck Type: {{$orders[0]->truck_type}} TRUCK NO: {{$orders[0]->truck_no}}
                </h5>
            </div>
            <div class="card-body">
                <span class="defined-title">{{$orders[0]->transporter_name}}</span>
                <p class="info">
                    <span>{{$orders[0]->phone_no}}</span>
                </p>
                
            </div>   
        </div>
    </div>

    <div class="col-md-4" >
        <div class="card bg-primary-600 no-border">
            <div class="card-header header-elements-inline">
                <h5 class="card-title info">Motor Boy: {{$orders[0]->motor_boy_first_name}} {{$orders[0]->motor_boy_last_name}} <span>{{$orders[0]->motor_boy_phone_no}}</span></h5>
            </div>
            <div class="card-body">
                <span class="defined-title">{{$orders[0]->driver_last_name}} {{$orders[0]->driver_first_name}}</span> 
                <p class="info">
                    <span>{{$orders[0]->driver_phone_number}}</span><br>                    
                </p>
                <!-- "driver_first_name": "Olatunji",
"driver_last_name": "Samuel",
"driver_phone_number": "08028955866",
"motor_boy_first_name": "Ajani",
"motor_boy_last_name": "Oyebola",
"motor_boy_phone_no": "08026236622", -->
            </div>   
        </div>
    </div>
</div>



<div class="card-header bg-white header-elements-inline">
    <h6 class="card-title font-weight-semibold">TIMESHEET OF EVENTS</h6>
</div>
<table class="table">
    <thead class="table-info">
        <tr class="font-weigth-semibold" style="font-size:10px;">
            <th class="text-center">GATED IN</th>
            <th class="text-center">ARRIVAL AT LOADING BAY</td>
            <th class="text-center">LOADING DURATION</th>
            <th class="text-center">DEPARTURE</th>
            <th class="text-center">GATED OUT</th>
        </tr>
        <tbody>
            <tr style="font-size:9px;" class="text-center font-weight-semibold">
                <td>{{$orders[0]->gate_in}}</td>
                <td>{{$orders[0]->arrival_at_loading_bay}}</td>
                <td>{{$orders[0]->loading_start_time}} - {{$orders[0]->loading_end_time}}</td>
                <td>{{$orders[0]->departure_date_time}}</td>
                <td>{{$orders[0]->gated_out}}</td>
            </tr>
        </tbody>
</table>
<table class="table table-bordered">
    <thead class="table-info">
        <tr class="font-weigth-semibold" style="font-size:10px;">
            <th class="text-center">#</th>
            <th class="text-center">LOCATION 1 WITH REMARK</th>
            <th class="text-center">LOCATION 2 WITH REMARK</th>
            <th class="text-center">TIME ARRIVED DESTINATION</th>
            <th class="text-center">OFFLOADING DURATION</th>
        </tr>
        <?php $counter = 0 ?>
        @if(count($tripEvents))
            @foreach($tripEvents as $event) 
                <?php 
                    $counter++;
                    $counter % 2 == 0 ? $css = 'font-weight-semibold' : $css = 'table-success font-weight-semibold';
                ?>
                <tr class="{{$css}} text-center" style="font-size:10px;">
                    <td>DAY {{$counter}}</td>
                    <td>
                        <span class="text-primary">{{$event->location_check_one}}<br></span>
                        {{$event->location_one_comment}}
                    </td>
                    <td>
                        <span class="text-primary">{{$event->location_check_two}}<br></span>
                        {{$event->location_two_comment}}
                    </td>
                    <td>{{$event->time_arrived_destination}}</td>
                    <td>{{$event->offload_start_time}} - {{$event->offload_end_time}}</td>
                </tr>
            @endforeach
        @else
        <tr>
            <td colspan="8">You havent added any event log to this trip</td>
        </tr>
        @endif
        <tbody>
            
        </tbody>
</table>
</form>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/trip-overview.js')}}"></script>
@stop

