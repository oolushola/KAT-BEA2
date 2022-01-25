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
@include('orders.partials._transloaded')

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

        <a href="{{ URL('/completed/not-drop-off') }}" class="mt-2 ml-4">Yet to drop off empty trips</a>
    </div>

    <form method="POST" id="frmCancelTrip">
    @csrf {!! method_field('PATCH') !!}
    <div class="table-responsive" id="contentPlaceholder" style="position:relative; z-index: 0">
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

                            <span data-toggle="modal" data-target=".eventLog" class="addEvent" id="{{ $trip->tracker }}" name="{{ $trip->loading_site }}"  title="{{ $trip->trip_id }}" value="{{ $trip->id }}" data-prod="{{ $trip->product }}">
                                <i class="icon-calendar52 text-success-600"></i>
                            </span>
                            <p class="m-0"> 
                                <span class="badge badge-primary p-1">{{$trip->loading_site}}</span> 
                                <!-- <span class="badge badge-success p-1">{{strtoupper($trip->truck_no)}}</span>  -->
                            </p>
                        </td>
                        <td>
                            <span class="font-weight-bold">
                                {{strtoupper($trip->truck_no)}} 
                                <i class="icon-git-compare transloadTruck" href=".transloader" data-toggle="modal" value="{{ $trip->trip_id }}" id="{{ $trip->transporter_id }}" data-value="{{ $trip->id }}"></i>  
                            </span>
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
$(function() {
    $('.transloadTruck').click(function() {
        $tripId = $(this).attr('value')
        $('#selectedTripPlaceHolder').text('Transload '+$tripId)
        $('#transporterId').val($(this).attr("id"))
        $('#transloadTripId').val($(this).attr('data-value'))

        $('.driverDetailsChanged :input[type="text"]').attr('disabled', true)

        $.get('/transloaded-trip-info', { trip_id: $tripId }, function(response) {
            var data = response.tripInfo
            var preview = response.preview
            $('#previousTruckNo').val(data[0].truck_no)
            $('#previousTruckType').val(data[0].truck_type)
            $('#previousTonnage').val(data[0].tonnage)

            $('#previousDriverName').val(data[0].driver_first_name+' '+data[0].driver_last_name)
            $('#previousDriverNo').val(data[0].driver_phone_number)
            $('#previousMotorBoy').val(data[0].motor_boy_first_name+' '+data[0].motor_boy_last_name+' '+data[0].motor_boy_phone_no)

            $('#previousTruckId').val(data[0].truck_id);
            $('#previousDriverId').val(data[0].driver_id);

            $('#previewLog').html(preview)
        })
    })

    $('#previousDriverNo').keyup(function($e) {
        $e.preventDefault();
        $driverPhoneNo = $(this).val()
        $driverName = $('#previousDriverName').val()
        $motorboy = $('#previousMotorBoy').val()
        $driverId = $('#previousDriverId').val()
        if($e.keyCode === 13) {
            $('#placeholderLabel').html('Please wait...')
            $.get('/update-drivers-info', { driver: $driverId, name: $driverName, phoneNo: $driverPhoneNo, motorBoy: $motorboy }, function(data) {
                if(data === 'updated') {
                    $('#placeholderLabel').html('Driver Info updated successfully.')
                    window.location = '/on-journey-trips';
                }
            })
        }
    })


    $('#transload').click(function($e) {
        $e.preventDefault();
        $truckNo = $('#truckNo').val();
        if($truckNo === '') {
            messageDisplay('Truck number is required.')
            return false;
        }
        $truckType = $('#truckType').val();
        if($truckType == '0') {
            messageDisplay('Truck type is required.')
            return false;
        }
        $tonnage = $('#tonnage').val();
        if($tonnage == '0') {
            messageDisplay('Truck tonnage is required.')
            return false;
        }
        $sameDriver = $('#sameDriverChecker').is(':checked');
        if(!$sameDriver) {
            $tDriverName = $('#tdriverNo').val();
            if($tDriverName === '') {
                messageDisplay('Driver full name is required.')
                return false;
            }
            $tdriverNo = $('#tdriverNo').val();
            if($tdriverNo === '') {
                messageDisplay('Driver phone number is required.')
                return false;
            }
        }
        $transloadingComment = $('#transloadinComment').val();
        if($transloadingComment === '') {
            messageDisplay('Reason for transloading is required.')
            
        }
        $('#placeholderLabel').html('<i class="icon-spinner3 spinner"></i> Please wait...').addClass('font-size-sm')
        $.post('/truck-transload', $('#frmTripTransload').serializeArray(), function(data) {
            if(data === 'truckInPipeline') {
                $('#placeholderLabel').html('You can\'t transload to a truck that is in our trip pipeline. ').addClass('font-size-sm')
                return false;
            }
            else if(data === 'sameTruckNo') {
                $('#placeholderLabel').html('Why do you want to transload to the same truck? Is it crack?').addClass('font-size-sm')
                return false;
            }
            else{
                if(data === 'transloadingCompleted') {
                    $('#placeholderLabel').html('Successfully transloaded').addClass('font-size-sm')
                    window.location='/on-journey-trips'
                }
            }
        })
    })

    $('#sameDriverChecker').click(function() {
        $checker = $(this).is(":checked");
        if($checker === true) {
            $('.driverDetailsChanged :input[type="text"]').attr('disabled', true)
        }
        else{
            $('.driverDetailsChanged :input[type="text"]').attr('disabled', false)
        }
    })

    function messageDisplay(message) {
        $('#placeholderLabel').html(message).addClass('font-weight-semibold font-size-xs text-success').fadeIn(2000).delay(3000).fadeOut(2000)

    }


});
</script>
@stop