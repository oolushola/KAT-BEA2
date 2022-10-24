@extends('layout')

@section('title') Kaya ::. Truck Availability @stop

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
        <div class="col-md-3 offset-md-6 col-sm-8 offset-2 col-5">
            <a href="{{URL('update-trip')}}"><button class="btn btn-warning font-weight-semibold" style="font-size:10px; border-radius:0; margin-left:-10px; margin-right:-10px; color:#000">BACK TO UPDATE TRIP</button></a>
        </div>
        
        </div>


        <h2 style="font-size:18px; padding:5px;" class="font-weight-semibold">TRUCK AVAILABILITY</sub></h2>
    
    <div class="panel-group" id="accordion">
        <?php $counter = 0; ?>
        @if(count($availableTrucks))
            <p><strong>Note:</strong> The following <strong>trucks</strong> are trucks available for gate in at their respective loading sites. Click<strong> 'GATE IN TRUCK' </strong> button to proceed with the trip order information</p>
        
        @foreach($availableTrucks as $truckAvailability)

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title font-weight-semibold" style="background:#f5f5f5; padding:5px; border:1px solid #ccc; font-size:13px;">
                    <?php
                        $newTruckNumber = str_replace(' ', '', $truckAvailability->truck_no);
                        $truckAvailabilityId = $truckAvailability->id;
                        $collapsibleTarget = $newTruckNumber.$truckAvailabilityId;
                        $counter += 1;
                    ?>
                    
                    
                    <a data-toggle="collapse" data-parent="#accordion" href="#{{$collapsibleTarget}}">
                        {{$truckAvailability->truck_no}}</a>

                    <span style="font-size:11px; font-weight:bold" class="text-danger-400">
                    {{$truckAvailability->truck_status}}</span>

                    <span style="font-size:10px;" ><a href="#truckAvailabilityStatusChange" id="{{$truckAvailability->id}}" title="{{$truckAvailability->truck_no}}" name="{{$truckAvailability->truck_status}}" data-toggle="modal" class="updateAvailabilityStatus">[Update Status]</a></span>

                   

                    <span class="d-none d-sm-block" style="font-size:10px; float:right">Profiled By: {{ucwords($truckAvailability->first_name)}}, {{ucwords($truckAvailability->last_name)}}</span>
                    <!-- <a href="{{URL('create-trip/with/'.base64_encode($newTruckNumber).'/'.base64_encode($truckAvailabilityId).' ')}}" class="list-icons-item text-primary-600" title="Gate this truck in"></a> -->
                    <p>
                        <form id="frmDeleteTruckAvailability">
                            <button href="#pregateHandler" data-toggle="modal" type="button" class="btn btn-primary font-weight-semibold priceConsent" style="font-size:10px;" data-ng="{{$truckAvailability->client_id}}, {{ $truckAvailability->destination_state_id}}, {{ $truckAvailability->truck_id}}, {{$truckAvailability->exact_location_id}},{{ base64_encode($truckAvailability->truck_no) }},{{ base64_encode($truckAvailability->id) }}">VERIFY PRICE</button>

                       
                            @csrf {!! method_field('DELETE') !!}
                            <button class="btn btn-danger removeTruckAvailability" style="font-size:10px; display:inline-table" id="{{$truckAvailability->id}}" ><i class="icon icon-x"></i>
                                REMOVE
                            </button>
                        </form>
                    </p>
                    

                </h4>
            </div>

            <div id="{{$collapsibleTarget}}" class="panel-collapse collapse in" style="padding-bottom:10px; margin-top:-10px;">
                <div class="panel-body table-responsive">
                    <table class="table table-bordered" style="font-size:11px;">
                        <tr>
                            <td class="font-weight-semibold">CLIENT</td>
                            <td>{{ucwords($truckAvailability->company_name)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">LOADING SITE</td>
                            <td>{{$truckAvailability->loading_site}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRUCK NO.</td>
                            <td>{{strtoupper($truckAvailability->truck_no)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRUCK TYPE</td>
                            <td>{{$truckAvailability->truck_type}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TONNAGE</td>
                            <td>{{$truckAvailability->tonnage / 1000}}<sub>(t)</sub></td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRANSPORTER'S NAME</td>
                            <td>{{$truckAvailability->transporter_name}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">TRANSPORTER'S NUMBER</td>
                            <td>{{$truckAvailability->phone_no}}</td>
                        </tr>
                        
                        <tr>
                            <td class="font-weight-semibold">DRIVER'S FULLNAME</td>
                            <td>{{strtoupper($truckAvailability->driver_first_name)}} {{strtoupper($truckAvailability->driver_last_name)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">DRIVER'S PHONE NUMBER</td>
                            <td>{{$truckAvailability->driver_phone_number}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">MOTOR BOY'S NAME</td>
                            <td>{{strtoupper($truckAvailability->motor_boy_first_name)}} {{strtoupper($truckAvailability->motor_boy_last_name)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">MOTOR BOY'S NUMBER</td>
                            <td>{{$truckAvailability->motor_boy_phone_no}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">DESTINATION</td>
                            <td>{{strtoupper($truckAvailability->state)}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">EXACT LOCATION</td>
                            <td>{{$truckAvailability->exact_location_id}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-semibold">PRODUCT</td>
                            <td>{{$truckAvailability->product}}</td>
                        </tr>
                        
                        
                    </table>
                </div>
            </div>
        </div>

        

        @endforeach

        @else

        <h3 class="text-warning-400">No truck is available in Availablity for Gate In</h3>

        @endif

    </div> 
    </div>

</div>


@include('truck-availability.partials._status_update')
@include('truck-availability.partials._pre_gate')



@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/master-data.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/truckAvailability.js')}}"></script>
@stop