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

td:first-child, .headcol {
    position: sticky;
    left: 0px;
    font-weight: bold;
    background-color: #fbfbfb;
    color:#000;
}
</style>
@stop
@section('main')

@include('orders._helpers')


<div class="card">
    <div class="page-title d-flex ml-2">
        <h4 class="text-info"><span class="font-weight-semibold">Uncompleted Trips Log</span></h4>
        <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        <select type="text" class="ml-2 font-weight-semibold p-2" id="filterSelection" style="border:1px solid #ccc; outline:none; font-size:11px;" />
            <option>Filter</option>
            <option value="ON JOURNEY">ON JOURNEY</option>
            <option value="ARRIVED DESTINATION">ARRIVED DESTINATION</option>
        </select>
        <input type="text" id="searchUncompletedTrips" class="ml-2 font-weight-semibold p-2" placeholder="What are you looking for?" style="border:1px solid #ccc; outline:none; font-size:11px; width:150px" />
    </div>

    <form method="POST" id="frmCancelTrip">
    @csrf {!! method_field('PATCH') !!}
    <div class="table-responsive" id="contentPlaceholder">
        <table class="table table-bordered">
            <thead class="table-info" style="font-size:11px; background:#000; color:#eee;">
                <tr class="font-weigth-semibold">
                    <th class="text-center headcol">TRIP INFO</th>
                    <th class="text-center">TRUCK INFO</th>
                    <th>DRIVER DETAILS</th>
                    <th class="text-center">MORNING VISIBILITY</th>
                    <th class="text-center">AFTERNOON VISIBILITY</th>
                    <th>TIME ARRIVED DESTINATION</th>
                    <th class="text-center">OFFLOADING DURATION</th>
                    <th>CURRENT STAGE</th>
                </tr>
            </thead>
            <tbody id="uncompletedTrips">
                <?php $counter = 0; ?>
                @if(count($onJourneyTrips))
                    @foreach($onJourneyTrips as $trip)
                    <?php 
                        $counter++;
                        $counter % 2 == 0 ? $css = ' font-weight-semibold ' : $css = 'order-table font-weight-semibold';
                        if($trip->tracker >= 5 && $trip->tracker <= 6){ $current_stage = 'ON JOURNEY';}
                        if($trip->tracker == 7){ $current_stage = 'ARRIVED DESTINATION';}
                    ?>
                        
                    <tr class="{{$css}} hover" style="font-size:10px;">
                        <td class="text-center">
                            <a href="{{URL('/trips/'.$trip->id.'/edit')}}" class="list-icons-item text-primary-600 mb-2" title="Update this trip">{{$trip->trip_id}}</a>

                            <a href="{{URL('way-bill/'.$trip->trip_id.'/'.str_slug($trip->loading_site))}}" class="list-icons-item text-warning-600" title="Waybill Status"><i class="icon-file-check text-warning-600"></i></a>

                            <span data-toggle="modal" data-target=".eventLog" class="addEvent" id="{{ $trip->tracker }}" name="{{ $trip->loading_site }}"  title="{{ $trip->trip_id }}" value="{{ $trip->id }}">
                                <i class="icon-calendar52 text-success-600"></i>
                            </span>
                            <p class="m-0"> 
                                <span class="badge badge-primary p-1">{{$trip->loading_site}}</span> 
                                <!-- <span class="badge badge-success p-1">{{strtoupper($trip->truck_no)}}</span>  -->
                            </p>
                        </td>
                        <td>
                            <span class="font-weight-bold">{{strtoupper($trip->truck_no)}}</span>
                            <p class="m-0"> 
                                <span class="badge badge-success p-1 mt-1">{{ strtoupper($trip->exact_location_id) }}</span>
                                <span class="badge badge-success p-1 mt-1">{{ strtoupper($trip->product) }}</span>
                            </p>
                        </td>
                        <td class="text-center">
                            <span class="font-weight-semibold">
                                {{strtoupper($trip->driver_first_name)}}, {{strtoupper($trip->driver_last_name)}}
                                {{$trip->driver_phone_number}}
                            </span><br>
                            @if($trip->motor_boy_phone_no)
                            <span class="badge font-weight-bold">
                                MTB: {{strtoupper($trip->motor_boy_first_name)}} {{strtoupper($trip->motor_boy_last_name)}}
                                {{$trip->motor_boy_phone_no}}
                            </span>
                            @endif
                        </td>                        
                        <td class="text-center">{{eventdetails($tripEvents, $trip, 'location_check_one')}} - {{eventdetails($tripEvents, $trip, 'location_one_comment')}}</td>
                        
                        <td class="text-center">{{eventdetails($tripEvents, $trip, 'location_check_two')}} - {{eventdetails($tripEvents, $trip, 'location_two_comment')}}</td>
                        <td class="text-center">
                            {{eventdetails($tripEvents, $trip, 'time_arrived_destination')}}
                        </td>
                        <td class="text-center">
                            {{eventdetails($tripEvents, $trip, 'offload_start_time')}} - 
                            {{eventdetails($tripEvents, $trip, 'offload_end_time')}}
                        </td>
                        <!-- <td></td> -->
                        <td class="font-weight-semibold">{{$current_stage}}</td>            
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

@include('orders.partials._event')

@stop

@section('script')
<script src="{{URL::asset('js/validator/trip-event.js')}}"></script>
<script type="text/javascript">
//$(function() {
    // $selectedCriteria = localStorage.getItem('filtered')
    // if($selectedCriteria) {
    //     $(`#uncompletedTrips tr`).filter(function() {
    //         $(this).toggle($(this).text().toLowerCase().indexOf($selectedCriteria) > -1)
    //     });
    // }

    // autosearch("change", "#filterSelection", "#uncompletedTrips")
    // autosearch("keyup", "#searchUncompletedTrips", "#uncompletedTrips")

    // function autosearch(event, searchBoxId, dataSetId) {
    //     $(searchBoxId).on(event, function() {
    //         var value = $(this).val().toLowerCase();
    //         if(event == "change") {
    //             localStorage.setItem('filtered', value)
    //         }
    //         $(`${dataSetId} tr`).filter(function() {
    //             $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    //         });
    //     });
    // }

    

//});
</script>
@stop