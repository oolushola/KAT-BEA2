@extends('layout')

@section('title')Kaya ::. Transporter @stop

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
            <h4>
                <a href="{{URL('transporter-log')}}">
                    <i class="icon-arrow-left52 mr-2"></i> 
                </a>
                <span class="font-weight-semibold">Transporter Log</span> - {{ $transporterInfo->transporter_name  }}</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Contextual classes -->
        <div class="card">
            <div class="table-responsive">
                <div class="row">
                    <div></div>
                </div>
                <table class="table table-bordered">
                    <thead class="table-info" style="font-size:12px">
                        <tr>
                            <th>SN</th>
                            <th>TRIP DETAILS</th>
                            <th>TRUCK INFO</th>
                            <th>DROVE BY</th>
                            <th>CONSIGNEE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($tripInformation))
                            <?php $count = 0; ?>
                            @foreach($tripInformation as $trip)
                                <?php 
                                    if($count % 2 == 0) {
                                        $css = 'table-success';
                                    }
                                    else {
                                        $css = '';
                                    }
                                    if($trip->tracker >= 5 && $trip->tracker <= 6) { $iconclass = 'icon-spinner spinner text-danger'; }
                                    if($trip->tracker == 7) { $iconclass = 'icon-truck text-info'; }
                                    if($trip->tracker == 8) { $iconclass = 'icon-checkmark2 text-success'; }
                                ?>
                                  
                                <tr class="{{ $css }} font-size-sm">
                                    <td>{{ $count+= 1}}</td>
                                    <td>
                                        <small class="font-size-sm font-weight-bold">{{ date('d-m-Y h:mA', strtotime($trip->gated_out)) }}</small>
                                        <h5 class="m-0">{{ $trip->trip_id }} <i class="{{$iconclass}}" title="Still On Journey"></i></h5>
                                        <small>{{ $trip->loading_site}}</small>
                                    </td>
                                    <td>
                                        <h6 class="m-0">{{ $trip->truck_no }}</h6>
                                        <small>{{ $trip->truck_type }} {{ $trip->tonnage / 1000 }}T</small>
                                    </td>
                                    <td>
                                        <h6 class="m-0">{{ $trip->driver_first_name }} {{ $trip->driver_last_name }}</h6>
                                        <span class="font-size-sm d-block">{{ $trip->driver_phone_number}}</span>
                                        @if($trip->motor_boy_first_name)
                                        <span class="badge badge-info pl-1 pr-1 font-size-sm">Motor Boy: {{ $trip->motor_boy_first_name }} {{ $trip->motor_boy_last_name }} {{ $trip->motor_boy_phone_no }}</span>
                                        <span class="font-size-sm"></span>
                                        @endif
                                    </td>
                                    <td>
                                        
                                        <h6 class="m-0">{{ $trip->customers_name }}</h6>
                                        <span class="d-block font-size-sm">{{ $trip->customer_no}}</span>
                                        <small>{{ trim($trip->state) }}, {{ $trip->exact_location_id }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">This transporter hasn't moved any cargo for kaya</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/transporter.js')}}"></script>
@stop