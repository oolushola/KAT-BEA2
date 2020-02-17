@extends('layout')

@section('title')Kaya ::. Event for {{$orderId}} at {{strtoupper($client_name)}} loading site @stop
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
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">{{$orderId}}</span> - {{strtoupper($client_name)}}</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View all Orders</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>View Client Specific History</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid))Update @else Add @endif Event</h5>
            </div>

            <div class="card-body">
                <form action="" method="POST" id="frmTripEvent" name="frmTripEvent">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" id="id" value="{{$recid->id}}" >
                        {!! method_field('PATCH') !!} 
                    @endif
                    <input type="hidden" name="trackerStatus" id="trackerStatus" value="{{$tracker}}" />
                    <input type="hidden" name="trip_id" value="{{$tripId}}" />
                    <input type="hidden" name="tracker" value="@if(isset($recid)){{$tracker}}@endif" id="tracker" />
                    <input type="hidden" name="current_date" value="{{date('Y-m-d')}}" />
                    
                    <div class="form-group">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" value="6" id="onjourney" name="on_journey" <?php if(isset($recid) && ($tracker>=6)) echo 'checked disabled'; ?>>On Journey

                                <input type="hidden" value="@if(isset($recid)){{$recid->journey_status}}@else 0 @endif" name="journey_status" id="onJourneyStatus" >
                            </label>
                        </div>
                    </div>
                    
                    <div id="journeyContainer">
                        <div class="form-group">
                            <label>Location check 1</label>
                            <input type="datetime-local" class="form-control" name="location_check_one" id="locationCheckOne" style="border-radius:0px;" value="<?php if(isset($recid)) {echo $recid->location_check_one; } else { echo date('Y-m-d\TH:i'); } ?>">

                            <input type="text" class="form-control" name="location_one_comment" id="localtionOneComment" placeholder="Enter your remark" style="border-radius:0px;" value="@if(isset($recid)){{$recid->location_one_comment}}@endif" >
                        </div>

                        <div class="form-group">
                            <label>Location check 2</label>
                            <input type="datetime-local" class="form-control" name="location_check_two" id="locationCheckTwo" style="border-radius:0px;" value="<?php if(isset($recid)) {echo $recid->location_check_two; } else { echo date('Y-m-d\TH:i'); } ?>">

                            <input type="text" class="form-control" name="location_two_comment" id="localtionTwoComment" placeholder="Enter your remark" style="border-radius:0px;" value="@if(isset($recid)){{$recid->location_two_comment}}@endif">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="arrivedDestination" value="7" @if(isset($recid) && ($tracker>=7)) checked disabled @else @endif>Arrived Destination?
                                <input type="hidden" name="destination_status" value="@if(isset($recid)){{$recid->destination_status}}@else 0 @endif" id="destinationArrival" >
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Time arrived at destination</label>
                        <input type="datetime-local" class="form-control" name="time_arrived_destination" id="timeArrivedDestination" disabled value="@if(isset($recid)){{$recid->time_arrived_destination}}@endif">
                    </div>

                    <div class="form-group">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="offloading" value="8" @if(isset($recid) && ($tracker>=8 )) checked disabled @else @endif>Offloading? 
                                <input type="hidden" name="offloading_status" id="offloadingStatus" value="@if(isset($recid)){{$recid->offloading_status}}@else 0 @endif">
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label>Offloading Starts</label>
                                <input type="datetime-local" class="form-control" name="offload_start_time" id="offloadStartTime" disabled value="@if(isset($recid)){{$recid->offload_start_time}}@endif"> 
                        </div>
                        <div class="form-group">
                            <label>Offloading Ends</label>
                                <input type="datetime-local" class="form-control" name="offload_end_time" id="offloadEndTime" disabled value="@if(isset($recid)){{$recid->offload_end_time}}@endif"> 
                        </div>
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateTripEvent" @if($recid->offload_end_time != '')disabled @endif>Update
                        @else
                        <button type="submit" class="btn btn-primary" id="addTripEvent">Add
                        @endif
                        Event
                            <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>

                    

                </form>
            </div>
        </div>
        <!-- /basic layout -->

    </div>

    <div class="col-md-7">
    &nbsp;

        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Events for {{$orderId}} at {{strtoupper($client_name)}}</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Date of Event</th>
                            <th>Location 1</th>
                            <th>Location 2</th>
                            <th>Destination</th>
                            <th colspan="2">Offload Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; $current_date = date("Y-m-d"); ?>
                        @if(count($tripEvents)) 
                            @foreach($tripEvents as $tripevent)
                            <?php
                                $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                                
                                if(($counter == 1) && ($current_date == $tripevent->current_date)) {
                                    $clientname = str_slug($client_name);
                                    $url = '/trip/'.$orderId.'/'.str_slug($client_name).'/'.$tripevent->id.'/edit';
                                    $icon = "<i class=\"icon-pencil7\"></i>";
                                }
                                else{
                                    $url="#";
                                    $icon = '<span class="error">Not permitted to edit</span>';
                                }
                            ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>{{$tripevent->current_date}}</td>
                                <td>{{$tripevent->location_check_one}} - {{$tripevent->location_one_comment}}</td>
                                <td>{{$tripevent->location_check_two}} - {{$tripevent->location_two_comment}}</td>  
                                <td>{{$tripevent->time_arrived_destination}}</td>
                                <td colspan="2">{{$tripevent->offload_start_time}} - {{$tripevent->offload_end_time}}</td>                              
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL($url)}}" class="list-icons-item text-primary-600">
                                         {!! $icon !!}
                                        </a>
                                    </div>
                                </td>
                            </tr>   
                            @endforeach
                        @else
                            <tr>
                                <td class="table-success" colspan="10">You've not add any event for this trip</td>
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
<script src="{{URL::asset('js/validator/trip-event.js')}}"></script>
@stop
