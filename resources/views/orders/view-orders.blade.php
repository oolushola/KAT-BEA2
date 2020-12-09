@extends('layout')

@section('title') Kaya ::. Trip Order Summary @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
.filterStyle { border: 1px solid #ccc; padding: 5px; font-size: 10px; margin: 0px 3px; width: 120px; outline:none }
</style>
@stop
@section('main')

@include('orders._helpers')


<div class="card">
    <div class="ml-3 mb-2 mt-2">{!! $pagination->links() !!}</div>

    <span id="responsePlace"></span>
    <div class="row ml-2 mr-2 mt-1">
       <div class="col-md-2">
           <div class="card hover filter" data-id="showClients">
               <div class="card-body font-weight-bold">CLIENT</div>
           </div>
       </div>
       <div class="col-md-2 ">
           <div class="card hover filter" data-id="showTransporters">
               <div class="card-body font-weight-bold">TRANSPORTERS</div>
           </div>
       </div>
       <div class="col-md-2">
           <div class="card hover filter" data-id="showTripStatus">
               <div class="card-body font-weight-bold">TRIP STATUS</div>
           </div>
       </div>
       <div class="col-md-2">
           <div class="card hover filter" data-id="show">
               <div class="card-body font-weight-bold">WAYBILL STATUS</div>
           </div>
       </div>
       <div class="col-md-2">
       <input type="text" class="form-control mt-2" placeholder="Search Record" id="searchDataset" />
            <div class="card-body font-weight-bold"></div>
       </div>
    </div>
    
    <div class="row mt-1 ml-3 mb-3 hidden display" id="showClients">
        <input type="date" id="clientTripDateFrom" class="filterStyle" />
        <input type="date" id="clientTripDateTo" class="filterStyle" />
        <select id="clients" class="filterStyle">
            <option value="">Clients</option>
            @foreach($clients as $client)
            <option value="{{ $client->id}}">{{ $client->company_name }}</option>
            @endforeach
        </select>
        <select id="clientLoadingSite" class="filterStyle">
            <option value="">Loading Site</option>
            <option value="2">GPF - APAPA</option>
        </select>
        
        <select id="clientTripStatus" class="filterStyle">
            <option value="">Trip Status</option>
            <option value="1">Gate In</option>
            <option value="2">At Loading Bay</option>
            <option value="3">At Loading Bay</option>
            <option value="4">Departed Loading Bay</option>
            <option value="5">Gate Out</option>
            <option value="6">On Journey</option>
            <option value="7">At Destination</option>
            <option value="8">Offloaded</option>
        </select>
        <button id="shootByClient" class="filterStyle btn-primary cursor" style="border:none; font-weight:bold" >SHOOT</button>
    </div>

    <div class="row mt-1 ml-3 mb-3 hidden display" id="showTransporters">
        <select id="transporter" class="filterStyle">
            <option value="">Transporters</option>
            @foreach($transporters as $transporter)
            <option value="{{ $transporter->id}}">{{ $transporter->transporter_name }}</option>
            @endforeach
        </select>
        <input type="date" id="transporterDateFrom" class="filterStyle" />
        <input type="date" id="transporterDateTo" class="filterStyle" />
        
        <select id="transporterTripStatus" class="filterStyle">
            <option value="">Trip Status</option>
            <option value="1">Gate In</option>
            <option value="2">At Loading Bay</option>
            <option value="3">At Loading Bay</option>
            <option value="4">Departed Loading Bay</option>
            <option value="5">Gate Out</option>
            <option value="6">On Journey</option>
            <option value="7">At Destination</option>
            <option value="8">Offloaded</option>
        </select>
        <button id="shootByTransporter" class="filterStyle btn-primary cursor" style="border:none; font-weight:bold" >SHOOT</button>
    </div>

    <div class="row mt-1 ml-3 mb-3 hidden display" id="showTripStatus">
        <select id="transporterTripStatus" class="filterStyle">
            <option value="">Trip Status</option>
            <option value="1">Gate In</option>
            <option value="2">At Loading Bay</option>
            <option value="3">At Loading Bay</option>
            <option value="4">Departed Loading Bay</option>
            <option value="5">Gate Out</option>
            <option value="6">On Journey</option>
            <option value="7">At Destination</option>
            <option value="8">Offloaded</option>
        </select>
        <button id="shootByTransporter" class="filterStyle btn-primary cursor" style="border:none; font-weight:bold" >SHOOT</button>
    </div>
    
    
    <form method="POST" id="frmCancelTrip">
    @csrf {!! method_field('PATCH') !!}
    <input type="hidden" name="page" value="">
    <div class="table-responsive mt-1" id="contentPlaceholder">
        <table class="table table-bordered">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee;">
                <tr class="font-weigth-semibold">
                    <th class="headcol">TRIP ID</th>
                    <th>LOADING SITE</td>
                    <th>SALES ORDER NO</th>
                    <th class="text-center">TRUCK INFO</th>
                    <th>TRANSPORTER'S NAME</th>
                    <th>DRIVER</th>
                    <th>MOTOR BOY</th>
                    <th>CUSTOMER</th>
                </tr>
            </thead>
            <tbody id="masterDataTable">
                <?php $counter = 0; ?>
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
                    <tr class="{{$css}} hover" style="font-size:10px;">
                        <td style="">
                            <a href="{{URL('/trips/'.$trip->id.'/edit')}}" class="list-icons-item text-primary-600" title="Update this trip">{{$trip->trip_id}}</a>

                            <a href="{{URL('way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site))}}" class="list-icons-item text-warning-600" title="Waybill Status">
                                <i class="icon-file-check text-warning-600"></i>
                            </a>
                            
                            <a href="{{URL('trip-overview/'.$trip->trip_id)}}" class="list-icons-item text-info-600" title="Preview Trip">
                                <i class="icon-eye8 text-info-600"></i>
                            </a>
                            <span class="list-icons-item">
                                @if($trip->tracker < 5)
                                    <i class="icon icon-x text-danger voidTrip" value="{{$trip->trip_id}}" title="Cancel Trip" id="{{$trip->id}}"></i>
                                @else
                                <i class="icon icon-checkmark2 voidTrip" value="{{$trip->trip_id}}" id="{{$trip->id}}" title="Gated Out"></i>
                                @endif
                            </span>
                            @if(isset($trip->gated_out))
                            <span class="badge badge-danger">{{ date('d-m-Y H:i:s', strtotime($trip->gated_out)) }}</span>
                            @endif
                        </td>
                        <td class="text-center"> {{ strtoupper($trip->loading_site) }} <br> 
                            <span class="badge badge-info">{{$current_stage}}</span>
                            <span class="badge badge-primary">{{strtoupper($trip->exact_location_id) }}</span>
                        </td>
                        <td class="text-center font-weight-semibold">
                            @foreach($tripWaybills as $salesNo)
                                @if($trip->id == $salesNo->trip_id)
                                <a href="{{URL::asset('assets/img/waybills/'.$salesNo->photo)}}" target="_blank" title="View waybill {{$salesNo->sales_order_no}}">
                                {{$salesNo->sales_order_no}}<br>
                                </a>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            {{strtoupper($trip->truck_no)}} 
                            @foreach($transloadedTrips as $transloaded)
                                @if($transloaded->trip_id == $trip->id)
                                    <i class="icon-toggle ml-3" data-popup="popover" data-placement="right" data-html="true" title="" data-content="<em>{{ ucwords($transloaded->reason_for_transloading) }}</em><br> {{ $transloaded->created_at }}" data-original-title="<b>{{ $transloaded->truck_no }}, {{ $transloaded->truck_type }}, {{ $transloaded->tonnage/1000 }}T</b>"></i>
                                @endif
                            @endforeach
                            <br>
                            <span class="badge badge-primary">{{strtoupper($trip->truck_type)}},  {{$trip->tonnage/1000}}T</span></td>
                        <td>{{strtoupper($trip->transporter_name)}} <span class="d-block">{{$trip->phone_no}}</span></td>
                        <td>
                            {{strtoupper($trip->driver_first_name)}} {{strtoupper($trip->driver_last_name)}}<br>
                            <span class="badge badge-info">{{$trip->driver_phone_number}}</span>
                        </td>
                        <td>
                            {{strtoupper($trip->motor_boy_first_name)}} {{strtoupper($trip->motor_boy_last_name)}} <br> 
                            <span class="badge badge-info">{{$trip->motor_boy_phone_no}}</span>
                        </td>
                        <td>
                            <span style="font-size:10px; color: blue">
                                {{$trip->customer_no}}</span><br> 
                                {{strtoupper($trip->customers_name)}} <br> 
                                {{$trip->customer_address}}
                        </td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/sort.js?v='.time() )}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/trip.js')}}"></script>

<script>
    $(window).scroll(function() {
        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            // ajax call get data from server and append to the div
        }
    });

    $('.filter').click(function() {
        $('.filter').removeClass('bg-danger')
        $(this).addClass('bg-danger')

        $showDataIdentity = $(this).attr('data-id');
        $('.display').addClass('hidden')
        $(`#${$showDataIdentity}`).removeClass('hidden')
    })


</script>
@stop