@extends('layout')

@section('title') Kaya ::. Trips @stop

@section('main')
@if(isset($recid))
<form method="POST" name="frmTrips" id="frmTrips" action="{{URL('trips', $recid[0]->id)}}">
@else
<form method="POST" name="frmTrips" id="frmTrips" action="{{URL('trips')}}">
@endif

    @csrf
    @if(isset($recid))
        <input type="hidden" name="id" id="id" value="{{$recid[0]->id}}">
        {!! method_field('PATCH') !!}
    @endif
    <input type="hidden" name="trip_status" value="1">

    
    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
    

    <input type="hidden" name="truckNumberChecker" id="truckNumberChecker" value="<?php if(isset($recid)){ echo 1; } else { echo 0; } ?>">
    <input type="hidden" name="transporterChecker" id="transporterChecker" value="<?php if(isset($recid)){ echo 1; } else{ echo 0; } ?>">
    <input type="hidden" name="driverChecker" id="driverChecker" value="<?php if(isset($recid)) { echo 1; } else { echo 0; } ?>">


    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            @if(isset($recid))
            <h6 class="card-title font-weight-semibold">UPDATE TRIP - {{$recid[0]->trip_id}}</h6>
            @else
            <h6 class="card-title font-weight-semibold">INITIATE TRIP</h6>
            @endif
        </div>
        <div class="row ml-2">
            <div class="font-weight-semibold error mt-2 col-md-2">
                @if(isset($recid))
                    @if($recid[0]->tracker >= 1)
                         Gate In <i class="icon-truck"></i>
                    @endif
                @else
                        <input type="radio" value="1" name="trip" class="tripStatus" checked> Gate In
                @endif
            </div>
            <div class="font-weight-semibold error mt-2 col-md-2">
                @if(isset($recid))
                    @if($recid[0]->tracker >= 2)
                        Arrival at loading bay <i class="icon-truck"></i>
                    @else
                        <input type="radio" value="2" name="trip" class="tripStatus"> Arrival at loading bay
                    @endif
                @else
                    <input type="radio" value="2" name="trip" class="tripStatus"> Arrival at loading bay
                @endif
            </div>
            <div class="error font-weight-semibold mt-2 col-md-2">
                @if(isset($recid))
                    @if($recid[0]->tracker >= 3)
                        Loading <i class="icon-truck"></i>
                    @else
                        <input type="radio" value="3" name="trip" class="tripStatus"> Loading
                    @endif
                @else
                    <input type="radio" value="3" name="trip" class="tripStatus"> Loading
                @endif
            </div>
            <div class="error font-weight-semibold mt-2 col-md-2">
                @if(isset($recid))
                    @if($recid[0]->tracker >= 4)
                        Departure <i class="icon-truck"></i>
                    @else
                        <input type="radio" value="4" name="trip" class="tripStatus" > Departure
                    @endif
                @else
                    <input type="radio" value="4" name="trip" class="tripStatus" > Departure
                @endif
            </div>
            <div class="error font-weight-semibold mt-2 col-md-2">
                @if(isset($recid))
                    @if($recid[0]->tracker >=5)
                        Gate out <i class="icon-truck"></i>
                    @else
                        <input type="radio" value="5" name="trip" class="tripStatus" > Gate out
                    @endif
                @else
                    <input type="radio" value="5" name="trip" class="tripStatus" > Gate out
                @endif
            </div>
            @if(isset($recid))
                <input type="hidden" value="@if($recid[0]->tracker >= 5){{$recid[0]->tracker}}@else{{''}}@endif" id="tracker" name="tracker" />
            @else
                <input type="hidden" value="1" id="tracker" name="tracker" />
            @endif
        </div>
    </div>

    @if(isset($recid) && ($recid[0]->tracker) >=1)
    <div class="card">
    @else
    <div class="card hidden" id="gateInContainer">@endif
    
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title font-weight-semibold">GATE IN 
                <span class="text-danger-400 font-weight-semibold hidden" id="gateInPlaceholder" style="font-size:11px;">
                <input type="datetime-local" class="form-control" name="gate_in" id="gateIn" value="<?php if(isset($recid)){ echo $recid[0]->gate_in; } else { echo date('Y-m-d\TH:i'); } ?>" >
            </span></h6>
            <span></span>

            <span style="font-size:8px; font-family:tahoma; cursor:pointer; color:blue; font-weight:bold" id="changeGateInTime">Change Time and Date of Gate In</span>
        </div>
        
        <div class="row ml-3 mr-3 mt-3">
            
            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Client *</label>
                    <select class="form-control" name="client_id" id="clientId">
                        <option value="">Choose Client</option>
                        @foreach($clients as $client)
                            @if(isset($recid) && ($recid[0]->client_id == $client->id))
                                <option value="{{$client->id}}" selected>
                                    {{strtoupper($client->company_name)}}
                                </option>
                            @else
                                <option value="{{$client->id}}">
                                    {{strtoupper($client->company_name)}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" id="loadingSiteContainer">
                    <label class="font-weight-semibold">Loading Site *</label>
                    <select class="form-control" name="loading_site_id" id="loadingSite">
                        <option value="">View all loading site</option>
                        @foreach($loadingsites as $loadingsite)
                            @if(isset($recid) && ($recid[0]->loading_site_id == $loadingsite->id))
                                <option value="{{$loadingsite->id}}" selected>
                                    {{strtoupper($loadingsite->loading_site)}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Truck No *</label>
                    <input type="text" value="<?php if(isset($recid)){ echo $recid[0]->truck_no; } else echo ''; ?>" class="form-control" name="truck_no" id="searchTruckNo">
                    
                    <input type="hidden" name="truck_id" id="truckIdValue" value="<?php if(isset($recid)){ echo $recid[0]->truck_id; } else echo ''; ?>">
                </div>
                <div class="table-responsive hidden" style="position:absolute; background:#f1f1f1; max-height:350px; border-radius:5px; margin-top:-20px; box-shadow:2px 2px 2px #ccc; z-index:10; font-size:11px; width:290px;" id="truckNoLists">
                    
                    <section id="closeTruckBank" style="font-size:11px; tect-decoration:underline; padding:5px; text-align:right; background:#fbfbfb; color:red; cursor:pointer; font-weight:bold; font-family:tahoma; font-size:10px;">Close</section>

                    <table class="table table-stripped" id="truckBank">
                        <tbody>
                            @if(count($trucks))
                            <?php $iterator = 0; ?>
                                @foreach($trucks as $truck)
                                    <?php $iterator+=1; 
                                        $iterator % 2 == 0 ? $css_style = 'table-success' : $css_style='';
                                    ?>
                                    <tr class="hover font-weight-semibold">
                                        <td id="{{$truck->id}}" class="truckNo">{{$truck->truck_no}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Truck Type *</label>
                    
                    <select id="truckType"  name="truck_type" class="form-control">
                        <option value="">Choose Truck Type</option>
                        @foreach($truckTypes as $specificTruckType)
                            @if(isset($recid) && $specificTruckType->truck_type == $truckType->truck_type)
                            <option value="{{$specificTruckType->truck_type}}" selected>{{$specificTruckType->truck_type}}</option>
                            @else
                            <option value="{{$specificTruckType->truck_type}}">{{$specificTruckType->truck_type}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Tonnage *<sub>(Kg)</sub></label>
                    <input type="text" class="form-control" value="<?php if(isset($recid)){ echo $recid[0]->tonnage; } else echo ''; ?>" name="tonnage" id="tonnage">
                    <input type="hidden" id="truckTonnage" name="truck_tonnage" value="">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Transporter Name *</label>
                    <input type="text" value="<?php if(isset($recid)){ echo $recid[0]->transporter_name; } else echo ''; ?>" name="transporter_name" id="searchTransporter" class="form-control">
                    <input type="hidden" name="transporter_id" id="transporterIdValue" value="<?php if(isset($recid)){ echo $recid[0]->transporter_id; } else echo ''; ?>">
                </div>

                <div class="table-responsive hidden" style="position:absolute; background:#f1f1f1; max-height:350px; border-radius:5px; margin-top:-20px; box-shadow:2px 2px 2px #ccc; z-index:10; font-size:11px; width:290px;" id="transporterList">

                <section id="closeTransporterBank" style="font-size:11px; tect-decoration:underline; padding:5px; text-align:right; background:#fbfbfb; color:red; cursor:pointer; font-weight:bold; font-family:tahoma; font-size:10px;">Close</section>

                    <table class="table table-stripped" id="transporterBank">
                        <tbody>
                            @if(count($transporters))
                            <?php $iterator = 0; ?>
                                @foreach($transporters as $transporter)
                                    <?php $iterator+=1; 
                                        $iterator % 2 == 0 ? $css_style = 'table-success' : $css_style='';
                                    ?>
                                    <tr class="hover font-weight-semibold">
                                        <td id="{{$transporter->id}}" class="transporters">{{$transporter->transporter_name}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                
            </div>
            <div class="col-md-4" id="transporterPhoneNumber">
                <div class="form-group">
                    <label class="font-weight-semibold">Transporter's Phone Number *</label>
                    <input type="text" class="form-control" name="transporter_phone_no" id="transporterNumber" value="<?php if(isset($recid)) { echo $recid[0]->phone_no;} ?>" >
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Driver's Name *</label>
                    <input type="text" class="form-control" value="<?php if(isset($recid)){ echo $recid[0]->driver_first_name.' '.$recid[0]->driver_last_name; } else echo ''; ?>" name="drivers_name" id="searchDriver">
                    <input type="hidden" id="driverIdValue" name="driver_id" value="<?php if(isset($recid)) { echo $recid[0]->driver_id; } else {echo ''; } ?>">
                </div>
                <div class="table-responsive hidden" style="position:absolute; background:#f1f1f1; max-height:350px; border-radius:5px; margin-top:-20px; box-shadow:2px 2px 2px #ccc; z-index:10; font-size:11px; width:290px;" id="driversList">

                <section id="closeDriverBank" style="font-size:11px; tect-decoration:underline; padding:5px; text-align:right; background:#fbfbfb; color:red; cursor:pointer; font-weight:bold; font-family:tahoma; font-size:10px;">Close</section>

                    <table class="table table-stripped"  id="driversBank">
                        <tbody>
                            @if(count($drivers))
                            <?php $iterator = 0; ?>
                                @foreach($drivers as $driver)
                                    <?php $iterator+=1; 
                                        $iterator % 2 == 0 ? $css_style = 'table-success' : $css_style='';
                                    ?>
                                    <tr class="hover font-weight-semibold">
                                        <td id="{{$driver->id}}" class="drivers">{{strtoupper($driver->driver_last_name)}} {{strtoupper($driver->driver_first_name)}} : {{$driver->driver_phone_number}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Driver's Number *</label>
                    <input type="text" class="form-control" name="drivers_phone_no" id="driverPhoneNumber" value="<?php if(isset($recid)) { echo $recid[0]->driver_phone_number; } ?>">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Motor Boy's Name</label>
                    <input type="text" name="motor_boy_name" id="motorBoyName" class="form-control" value="<?php if(isset($recid)) { echo $recid[0]->motor_boy_first_name.' '.$recid[0]->motor_boy_last_name; } ?>">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Motor Boy's Number</label>
                    <input type="text" class="form-control" name="motor_boy_number" id="motorBoyNumber" value="<?php if(isset($recid)) { echo $recid[0]->motor_boy_phone_no; } ?>">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group" id="productContainer">
                    <label class="font-weight-semibold">Product *</label>
                    
                        <select class="form-control" name="product_id" id="productId">
                            <option value="">View all products</option>
                            @foreach($products as $product)
                            @if(isset($recid) && ($recid[0]->product_id == $product->id))
                            <option value="{{$product->id}}" selected>{{$product->product}}</option>
                            @else
                            <option value="{{$product->id}}">{{$product->product}}</option>
                            @endif
                            @endforeach
                        </select>
                    
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Destination: State on AX *</label>
                    <select class="form-control" name="destination_state_id" id="destinationState">
                        <option value="">View all states</option>
                        @foreach($states as $state)
                        @if(isset($recid) && ($recid[0]->destination_state_id == $state->regional_state_id))
                        <option value="{{$state->regional_state_id}}" selected>{{$state->state}}</option>
                        @else
                        <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group" id="exactLocationHolder">
                    <label class="font-weight-semibold">Destination *</label>
                    <select class="form-control" name="exact_location_id" id="exactLocation">
                        <option value="">Exact destination</option>
                        @if(isset($recid))
                            @foreach($exactdestinations as $destination)
                                @if($destination->transporter_to_state_id == $recid[0]->destination_state_id)
                                    @if($recid[0]->exact_location_id == $destination->transporter_destination)
                                    <option value="{{$destination->transporter_destination}}" selected>{{$destination->transporter_destination}}</option>
                                    @else
                                    <option value="{{$destination->id}}">{{$destination->transporter_destination}}</option>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-semibold">Acount Officer</label>
                    <input type="text" class="form-control" name="account_officer" id="account_officer" value="<?php if(isset($recid)) { echo $recid[0]->account_officer; } else { echo ''; } ?>">
                </div>
            </div>
        </div>

            <div class="text-left  ml-4 mr-3 mb-3">
                @if(!isset($recid))
                <button type="submit" class="btn btn-primary" name="registerTrip" id="registerTrip">Register Trip</button>
                @endif
            </div>

    </div>

    @if(isset($recid) && ($recid[0]->tracker)>=1)
        @include('orders.partials._arrivalatloadingbay')
    @endif

    @if(isset($recid) && ($recid[0]->tracker)>=2 && ($recid[0]->loading_start_time == '' || $recid[0]->loading_end_time == ''))
        @include('orders.partials._loading')
    @elseif(isset($recid) && ($recid[0]->tracker)>=2 && ($recid[0]->loading_start_time != '' || $recid[0]->loading_end_time != ''))
        @include('orders.partials._loading')
    @endif



    @if(isset($recid) && ($recid[0]->tracker)>=3)
        @include('orders.partials._departure')
    @endif

    @if(isset($recid) && ($recid[0]->tracker)>=4)
        @include('orders.partials._gatedout')
        <input type="hidden" name="day" value="{{date('d')}}">
        <input type="hidden" name="month" value="{{date('F')}}">
        <input type="hidden" name="year" value="{{date('Y')}}">
    @endif
        
    <input type="hidden" id="tripId" value="@if(isset($recid)){{$recid[0]->trip_id}}@endif" >
    
</form>
<div id="loader">@include('errors')</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/tripvalidations.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/truckAvailability.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/trip.js')}}"></script>
@stop