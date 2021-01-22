<div class="modal fade eventLog">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" id="frmTripEvent" name="frmTripEvent" enctype="multipart/form-data" action="{{URL('trip-event')}}">
                            @csrf
                            <input type="hidden" name="_method" value="" class="d-none" id="patchMethod">
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
                                                <option value="{{ $lga->lga_name }}">{{ $lga->lga_name }}</option>
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
                                    <input type="hidden" id="tripId" name="trip_id" />
                                    <input type="hidden" id="orderId" name="orderId" />
                                    <input type="hidden" id="loadingSite" name="loading_site" />
                                    <input type="hidden" name="id" id="id" value="" >
                                    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                                    <input type="hidden" name="current_date" value="{{date('Y-m-d')}}" />
                                    <input type="hidden" id="productCarried" value="" />

                                    <div id="journeyContainer">
                                        <div class="row">
                                            <div class="col-md-3" id="locationCheckOne">
                                                <div class="form-group">
                                                    <label>Location Check One</label>
                                                    <input type="datetime-local" class="form-control" name="location_check_one" id="morningVisibility" style="border-radius:0px;">

                                                    <input type="text" class="form-control" name="location_one_comment" id="morningComment" placeholder="Enter your remark" style="border-radius:0">

                                                    <select class="form-control" style="border-radius:0" name="morning_issue_type" id="morningIssueType">
                                                        <option value="">Journey Issue Type</option>
                                                        @foreach($onjourneyIssueTypes as $journeyIssueType)
                                                        <option value="{!! $journeyIssueType->issue_type !!}">{{ $journeyIssueType->issue_type }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3" id="locationCheckTwo">
                                                <div class="form-group">
                                                    <label>Location Check Two</label>
                                                    <input type="datetime-local" class="form-control" name="location_check_two" id="afternoonVisibility" style="border-radius:0px;" value="<?php if(isset($recid)) {echo $recid->location_check_two; } ?>">

                                                    <input type="text" class="form-control" name="location_two_comment" id="afternoonRemark" id="afternoonComment" placeholder="Enter your remark" style="border-radius:0px;" value="@if(isset($recid)){{$recid->location_two_comment}}@endif">

                                                    <select class="form-control" style="border-radius:0" name="afternoon_issue_type" id="afternoonIssueType">
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
                                    
                                    <div id="waybillAndEirPlaceholder" class="d-none">
                                        <p class="font-weight-bold">Upload Waybills / EIR <span class="d-block font-weight-semibold mt-2" id="addMoreWaybillAndEir">Add More</span></p>
                                        <div class="d-block" id="waybillAndEirHolder">
                                            <input type="file" name="received_waybill_and_eir[]" id="receivedWaybillAndEir" class="font-size-xs mb-1 d-inline">
                                        </div>
                                        <textarea name="waybillRemark" id="" class="col-md-3 col-sm-12" placeholder="Comment / Remark"></textarea>
                                    </div>
                                    
                                    <div class="text-right">
                                        <span id="loaderEvent"></span>
                                        <button type="submit" class="btn btn-primary d-none" id="updateTripEvent">Update Event
                                        <i class="icon-paperplane ml-2"></i></button>
                                        
                                        <button type="submit" class="btn btn-primary" id="addTripEvent">Add Event
                                            <i class="icon-paperplane ml-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> 
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card" id="eventLogListings"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>  
</div>
