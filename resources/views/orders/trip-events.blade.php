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
    <div class="col-md-12">
        &nbsp;
        <form action="" method="POST" id="frmTripEvent" name="frmTripEvent">
            @csrf
            @if(isset($recid))
                <input type="hidden" name="id" id="id" value="{{$recid->id}}" >
                {!! method_field('PATCH') !!} 
            @endif
            <!-- Basic layout-->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5>
                        <select style="width:150px; border: 1px solid #ccc; padding:5px; font-size:11px; outline:none" name="tracker" id="tracker">
                            <option value="">Choose Trip Status</option>
                            <option value="6">On Journey</option>
                            <option value="7">Arrived Destination</option>
                            <option value="8">Offloading</option>
                        </select>
                        <select style="width:150px; border: 1px solid #ccc; padding:5px; font-size:11px; outline:none" name="lga" id="lga">
                            <option value="">Local Government Area</option>
                            @foreach($lgas as $lga)
                                @if(isset($recid))
                                    @if(($recid->afternoon_lga != "") && $recid->afternoon_lga == $lga->lga_name)
                                    <option value="{{ $lga->lga_name }}" selected>{{ $lga->lga_name }}</option>
                                    @elseif(($recid->afternoon_lga == "") && ($recid->morning_lga != "") && ($recid->morning_lga == $lga->lga_name))
                                    <option value="{{ $lga->lga_name }}" selected>{{ $lga->lga_name }}</option>
                                    @else
                                    <option value="{{ $lga->lga_name }}">{{ $lga->lga_name }}</option>
                                    @endif
                                @else
                                <option value="{{ $lga->lga_name }}">{{ $lga->lga_name }}</option>
                                @endif
                            @endforeach
                        </select> 
                        <input type="radio" class="ml-3 visibility" value="morningVisibility" name="visibility" />
                            <span class="font-size-sm ml-1 font-weight-semibold">Morning Visibility</span>
                        <input type="radio" class="ml-3 visibility" value="afternoonVisibility" name="visibility" />
                            <span class="font-size-sm ml-1 font-weight-semibold">Afternoon Visibility</span>

                        <input type="hidden" id="visibility" value="">
                    </h5>
                </div>

                <div class="card-body">
                    <input type="hidden" id="trackerStatus" value="{{$tracker}}" />
                    <input type="hidden" name="trip_id" value="{{$tripId}}" />
                    <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id }}">
                    <!-- <input type="text" name="tracker" value="@if(isset($recid)){{$tracker}}@endif" id="tracker" /> -->
                    <input type="hidden" name="current_date" value="{{date('Y-m-d')}}" />

                    <div id="journeyContainer">
                        <div class="row">
                            <div class="col-md-3" id="locationCheckOne">
                                <div class="form-group">
                                    <label>Location Check One</label>
                                    <input type="datetime-local" class="form-control" name="location_check_one" id="morningVisibility" style="border-radius:0px;" value="<?php if(isset($recid)) {echo $recid->location_check_one; } ?>">


                                    <input type="text" class="form-control" name="location_one_comment" id="morningComment" placeholder="Enter your remark" style="border-radius:0" value="@if(isset($recid)){{$recid->location_one_comment}}@endif" >

                                    <select class="form-control" style="border-radius:0" name="morning_issue_type">
                                        <option value="">Journey Issue Type</option>
                                        @foreach($onjourneyIssueTypes as $journeyIssueType)
                                        @if(isset($recid) && $recid->morning_issue_type == $journeyIssueType->issue_type)
                                        <option value="{!! $journeyIssueType->issue_type !!}" selected>
                                            {{ $journeyIssueType->issue_type }}
                                        </option>
                                        @else
                                        <option value="{!! $journeyIssueType->issue_type !!}">{{ $journeyIssueType->issue_type }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3" id="locationCheckTwo">
                                <div class="form-group">
                                    <label>Location Check Two</label>
                                    <input type="datetime-local" class="form-control" name="location_check_two" id="afternoonVisibility" style="border-radius:0px;" value="<?php if(isset($recid)) {echo $recid->location_check_two; } ?>">

                                    <input type="text" class="form-control" name="location_two_comment" id="afternoonRemark" id="afternoonComment" placeholder="Enter your remark" style="border-radius:0px;" value="@if(isset($recid)){{$recid->location_two_comment}}@endif">

                                    <select class="form-control" style="border-radius:0" name="afternoon_issue_type">
                                        <option value="">Journey Issue Type</option>
                                        @foreach($onjourneyIssueTypes as $journeyIssueType)
                                        @if(isset($recid) && $recid->afternoon_issue_type == $journeyIssueType->issue_type)
                                        <option value="{!! $journeyIssueType->issue_type !!}" selected>
                                            {{ $journeyIssueType->issue_type }}
                                        </option>
                                        @else
                                        <option value="{!! $journeyIssueType->issue_type !!}">{{ $journeyIssueType->issue_type }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if(isset($tripDestinationchecker->time_arrived_destination))
                            <!-- <input type="hidden" value="{{ $tripDestinationchecker->time_arrived_destination }}" id="tadChecker" name="tad"> -->
                            @endif
                                
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Time Arrived Destination</label>
                                            <input type="datetime-local" style="border-radius:0; font-size:11px" class="form-control" name="time_arrived_destination" id="timeArrivedDestination" value="@if(isset($recid)){{$recid->time_arrived_destination}}@endif">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Gate-in destination timestamp</label>
                                            <input type="datetime-local" style="border-radius:0; font-size:11px" class="form-control" name="gate_in_destination_timestamp" id="gateInDestinationTimestamp" disabled value="@if(isset($recid)){{$recid->gate_in_time_destination}}@endif">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Offload Starts</label>
                                                <input type="datetime-local" style="border-radius:0; font-size:11px" class="form-control" name="offload_start_time" id="offloadStartTime" disabled value="@if(isset($recid)){{$recid->offload_start_time}}@endif">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="mt-2">Offload Ends</label>
                                                <input type="datetime-local" style="border-radius:0; font-size:11px" class="form-control" name="offload_end_time" id="offloadEndTime" disabled value="@if(isset($recid)){{$recid->offload_end_time}}@endif">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="mt-2">Where did it Offload?</label>
                                                <input type="text" class="form-control" name="offloaded_location" id="offloadedLocation"  style="border-radius:0" value="@if(isset($recid)){{$recid->offloaded_location}}@endif" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="mt-2">Offload Issue Type</label>
                                                <select class="form-control" style="border-radius:0" disabled name="offload_issue_type" id="offloadIssueType">
                                                    <option value="">Choose</option>
                                                    @foreach($offloadIssueTypes as $offloadIssueType)
                                                    <option value="{!! $offloadIssueType->issue_type !!}">{{ $offloadIssueType->issue_type }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                    </div>
                                        
            
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateTripEvent" @if($recid->offload_end_time != '' && Auth::user()->role_id != 1)disabled @endif>Update
                        @else
                        <button type="submit" class="btn btn-primary" id="addTripEvent">Add
                        @endif
                        Event
                            <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>
                    
                </div>
            </div>
            <!-- /basic layout -->
        </form>
    </div>

    <div class="col-md-12">
        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Events Log of  {{$orderId}} at {{strtoupper($client_name)}}</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Date of Event</th>
                            <th>Morning Visibility</th>
                            <th>Afternoon Visibility</th>
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
                            <tr class="{{$css}}" style="font-size:11px">
                                <td>{{$counter}}</td>
                                <td>{{$tripevent->current_date}}</td>
                                <td>
                                    <p class="m-0">{{ date('d/m/Y - h:iA', strtotime($tripevent->location_check_one)) }} 
                                    ({{$tripevent->location_one_comment}})</p>
                                    <span class="badge badge-primary block">Lga: {{ $tripevent->morning_lga }}</span>

                                    @if($tripevent->morning_issue_type)
                                    <span class="badge badge-danger block">Issue: {{ $tripevent->morning_issue_type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tripevent->location_check_two)
                                    <p class="m-0">{{ date('d/m/Y - h:iA', strtotime($tripevent->location_check_two)) }} 
                                    ({{$tripevent->location_two_comment}})</p>
                                    <span class="badge badge-primary block">Lga: {{ $tripevent->afternoon_lga }}</span>
                                    @endif
                                    @if($tripevent->afternoon_issue_type)
                                    <span class="badge badge-danger block">Issue: {{ $tripevent->afternoon_issue_type }}</span>
                                    @endif
                                </td>  
                                <td>
                                    @if($tripevent->time_arrived_destination)
                                    {{ date('d/m/Y - h:iA', strtotime($tripevent->time_arrived_destination)) }}
                                    @endif
                                    @if($tripevent->gate_in_time_destination)
                                    <span class="d-block badge badge-primary">{{ date('d/m/Y - h:iA', strtotime($tripevent->gate_in_time_destination)) }}</span>
                                    @endif
                                </td>
                                <td colspan="2">
                                    @if($tripevent->offload_end_time)
                                    <p class="m-0">{{ date('d/m/Y - h:iA', strtotime($tripevent->offload_start_time)) }} - 
                                    {{ date('d/m/Y - h:iA', strtotime($tripevent->offload_end_time)) }}</p>
                                    @endif

                                    @if($tripevent->offload_issue_type )
                                    <span class="badge badge-danger">Issue: {{ $tripevent->offload_issue_type }}</span>
                                    @endif
                                </td>

                                    
                                    
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
