@extends('layout')

@section('title') Kaya ::. TRIPS FOR OFFLOADING @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
input::placeholder{
    font-size:18px;
    font-weight:bold;
}
</style>
@stop
@section('main')

<div class="row">
    <div class="col-md-12">
    <input type="text" class="form-control" placeholder="QUICK SEARCH" id="searchTrip">

    </div>
</div>
<div class="row" id="myTable">
    
    @if(count($onJourneyTrips))
        <?php $count = 0; ?>
        @foreach($onJourneyTrips as $offloadingClerkTrips)
            <?php $count++;
                $count % 2 == 0 ? $class='table-primary' : $class = 'table-secondary';
            ?>
            <section class="col-md-4 col-sm-12 col-12 mb-4 {{$class}} ">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">
                                    <span class="d-block">Offloading Clerk</span>
                                    @foreach($adHocStaffList as $assignedLocation)
                                        @if($assignedLocation->exact_location == $offloadingClerkTrips->exact_location_id)
                                            <span class="text-primary d-block">{{ ucfirst($assignedLocation->first_name)}} {{ $assignedLocation->last_name }} </span>
                                        @endif
                                    @endforeach
                                    
                                </td>
                                <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">Destination</span>{{ $offloadingClerkTrips->exact_location_id }}</td>
                            <tr>
                            <tr>
                                <td class="font-weight-bold">
                                    Trip Details
                                    <div>
                                        <span class="badge badge-success">
                                            Account Manager: 
                                            @foreach($accountofficers as $user)
                                                @if($user->id == $offloadingClerkTrips->account_officer_id)
                                                    {{ $user->first_name }} {{ substr($user->last_name, 0, 1) }}.
                                                @endif
                                            @endforeach
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="defaultInfo">
                                        <span class="font-weight-bold">{{ $offloadingClerkTrips->trip_id }}</span>
                                        <span class="d-block font-size-sm"><strong>Gated Out</strong>: {{ date('d-m-Y', strtotime($offloadingClerkTrips->gated_out)) }}</span>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><span class="font-weight-semibold">Transporter: </span>{{ $offloadingClerkTrips->transporter_name }}</td>
                            </tr>
                            
                            <tr>
                                <td class="font-weight-bold">
                                    Truck Details
                                    <div>
                                        <span class="font-size-xs">
                                            DRV: {{ $offloadingClerkTrips->driver_first_name }}, {{ $offloadingClerkTrips->driver_phone_number }}
                                        </span><br>
                                        @if(isset($offloadingClerkTrips->motor_boy_first_name))
                                        <span class="font-size-xs">
                                            MB: {{ $offloadingClerkTrips->motor_boy_first_name }}, {{ $offloadingClerkTrips->motor_boy_phone_no }}
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                <h6 class="mb-0">
                                    <span class="defaultInfo">
                                        <span class="text-primary">{{ $offloadingClerkTrips->truck_no }}</span>
                                        <span class="d-block font-size-sm "><strong>Tonnage</strong>: {{$offloadingClerkTrips->tonnage/1000}}T</span>
                                        <span class="d-block font-size-sm "><strong>Product Carried</strong>: {{ $offloadingClerkTrips->product }}</span>
                                    </span>
                                </h6>
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="font-weight-bold">Consignee Details</td>
                                <td>
                                <h6 class="mb-0">
                                    <span class="defaultInfo">
                                        <span class="text-primary">{{ ucwords($offloadingClerkTrips->customers_name) }}</span>
                                        <span class="d-block font-size-sm "><strong>Phone No</strong>: {{$offloadingClerkTrips->customer_no}}</span>
                                    </span>
                                </h6>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Waybill Information</td>
                                <td>
                                    @foreach($tripWaybills as $waybillInfo)
                                    @if($waybillInfo->trip_id == $offloadingClerkTrips->id)
                                    <span class="d-block font-size-sm">{{ $waybillInfo->sales_order_no }} {{ $waybillInfo->invoice_no }}</span>
                                    @endif
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 7)
                                    <button id="{{$offloadingClerkTrips->id}}" title="{{$offloadingClerkTrips->trip_id}}" class="btn btn-primary remarkOnTrips font-weight-bold font-size-sm" data-toggle="modal" href="#remarkOfOffloadedTrip">REMARK</button>
                                    @endif
                                </td>
                                <td id="forNoUser">
                                    
                                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id==4)
                                    <input type="hidden" value="{{Auth::user()->id}}" id="userIdentifier">
                                    <button id="{{$offloadingClerkTrips->id}}" title="{{$offloadingClerkTrips->exact_location_id}}"  class="btn btn-warning nofifyAdHoc font-weight-bold font-size-sm">NOTIFY THEM <i class="icon-alarm"></i></button>
                                    @endif
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                
                

            </section>
        @endforeach
    @else
    <section class="col-md-12">We do not have any trucks on journey at this time.</section>
    @endif

</div>

<div id="remarkOfOffloadedTrip" class="modal fade" >
    <form method="POST" enctype="multipart/form-data" id="frmUpdateTripEvent" action="{{URL('update-trip-event-offloading-clerk')}}">
    @csrf {!! method_field('PATCH') !!}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding:5px; background:#324148">
                <h5 class="font-weight-sm font-weight-bold text-warning" id="titlePlace"></h5>
                <span class="ml-2">
                    <input type="hidden" name="trip_id" value="" id="tripId">
                </span>
                <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                
            </div>
            
            <div class="modal-body">
                <div class="row ml-3 mr-3 mt-3 show" id="percentHolder">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Time Arrived Destination</label>
                            <input type="datetime-local" class="form-control" name="time_arrived_destination" id="timeArrivedDestination">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Time Offloading Started</label>
                            <input type="datetime-local" class="form-control" name="time_offloading_started" id="timeOffloadStarted">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Time Offloading Ended</label>
                            <input type="datetime-local" class="form-control" name="time_offloading_end" id="timeOffloadingEnded">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Where did this truck offloaded?</label>
                            <input type="text" class="form-control" name="offloaded_location" id="offloadedLocation">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-semibold">Were you able to collect this waybill?</label>
                            <span class="d-block">
                                <input type="radio" class="waybillChecker" name="waybillChecker" title="collected">Yes &nbsp; &nbsp; <input type="radio" class="waybillChecker"  name="waybillChecker" title="notCollected">No
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6 hidden" id="proofOfWaybillUpload">
                        <div class="form-group">
                            <label class="font-weight-semibold">Upload proof of signed waybill collected</label>
                            <input type="file" name="recieved_waybill" id="receivedWaybill">
                        </div>
                    </div>

                    <div class="col-md-6 hidden" id="waybillNotCollected">
                        <div class="form-group">
                            <label class="font-weight-semibold">Why were you not able to collect this waybill?</label>
                            <textarea class="form-control" name="waybill_not_collected" id="waybilleNotReceived"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="waybillStatus" value="" name="waybill_status">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-semibold">&nbsp; &nbsp;</label><br>
                            <button type="submit" class="btn btn-lg btn-primary d-block font-weight-sm font-weight-bold" id="updateTripEvent">UPDATE CHANGES</button>
                        </div>
                    </div>
                    <span id="messageHolder" class="d-block"><span>

                </div>
            </div>

        </div>
    </div>  
    </form>
    
    

</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL('/js/validator/offloading-clerk.js')}}"></script>
@stop