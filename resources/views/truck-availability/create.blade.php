@extends('layout')

@section('title') Kaya ::. Trips @stop

@section('main')
<form method="POST" name="frmTruckAvailability" id="frmTruckAvailability">
    @csrf
    <input type="hidden" name="truckNumberChecker" id="truckNumberChecker" value="0">
    <!-- <input type="hidden" name="transporterChecker" id="transporterChecker" value="0"> -->
    <input type="hidden" name="driverChecker" id="driverChecker" value="0">
    
    <div class="card">

        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title font-weight-semibold">TRUCK AVAILABILITY: DETAILS</h6>
            <span></span>
            <span class="text-danger-400 font-weight-semibold" style="font-size:11px;">Note: All fields marked * are compulsory</span>
        </div>
            <div class="row ml-3 mr-3 mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-semibold">Client *</label>
                        <select class="form-control" name="client_id" id="clientId">
                            <option value="">Choose Client</option>
                            @foreach($clients as $client)
                                @if(isset($recid) && ($recid->client_id == $client->id))
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
                                @if(isset($recid) && ($recid->loading_site_id == $loadingsite->id))
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
                        <input type="text" class="form-control" name="truck_no" id="searchTruckNo">
                        <input type="hidden" name="truck_id" id="truckIdValue">
                    </div>
                    <div class="table-responsive hidden" style="position:absolute; background:#f1f1f1; max-height:350px; border-radius:5px; margin-top:-20px; box-shadow:2px 2px 2px #ccc; z-index:10; font-size:11px; width:290px;" id="truckNoLists">
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
                        <!-- <input type="text" id="truckType"  name="truck_type" class="form-control" value="<?php if(isset($recid)) { echo $truckType[0]['truck_type']; } ?>"> -->
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
                        <input type="text" class="form-control" value="<?php if(isset($recid)) { echo $truckType[0]['tonnage']; } ?>" name="tonnage" id="tonnage">
                        <input type="hidden" id="truckTonnage" name="truck_tonnage" value="">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-semibold">Transporter Name *</label>
                        <select name="transporter_id" id="transporterIdValue" class="form-control">
                            <option value="">Choose a Transporter</option>
                            @foreach($transporters as $transporter)
                                @if(isset($recid) && $recid[0]->transporter_id == $transporter->id)
                                <option value="{{$transporter->id}}" selected>{{ucwords($transporter->transporter_name)}}</option>
                                @else
                                <option value="{{$transporter->id}}">{{ucwords($transporter->transporter_name)}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                </div>
                <div class="col-md-4" id="transporterPhoneNumber">
                    <div class="form-group">
                        <label class="font-weight-semibold">Transporter's Phone Number *</label>
                        <input type="text" class="form-control" name="transporter_phone_no" id="transporterNumber" value="<?php if(isset($recid)) { echo $transporterNumber->phone_no;} ?>" >
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-semibold">Driver's Name *</label>
                        <input type="text" class="form-control" name="drivers_name" id="searchDriver">
                        <input type="hidden" id="driverIdValue" name="driver_id">
                    </div>
                    <div class="table-responsive hidden" style="position:absolute; background:#f1f1f1; max-height:350px; border-radius:5px; margin-top:-20px; box-shadow:2px 2px 2px #ccc; z-index:10; font-size:11px; width:290px;" id="driversList">
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
                        <input type="text" class="form-control" name="drivers_phone_no" id="driverPhoneNumber" value="<?php if(isset($recid)) { echo $driver->driver_phone_number; } ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-semibold">Motor Boy's Name</label>
                        <input type="text" name="motor_boy_name" id="motorBoyName" class="form-control" value="<?php if(isset($recid)) { echo $driver->motor_boy_first_name.' '.$driver->motor_boy_last_name; } ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-semibold">Motor Boy's Number</label>
                        <input type="text" class="form-control" name="motor_boy_number" id="motorBoyNumber" value="<?php if(isset($recid)) { echo $driver->motor_boy_phone_no; } ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group" id="productContainer">
                        <label class="font-weight-semibold">Product *</label>
                        
                            <select class="form-control" name="product_id" id="productId">
                                <option value="">View all products</option>
                                @foreach($products as $product)
                                @if(isset($recid) && ($recid->product_id == $product->id))
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
                            @if(isset($recid) && ($recid->destination_state_id == $state->regional_state_id))
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
                                    @if($destination->transporter_to_state_id == $recid->destination_state_id)
                                        @if($recid->exact_location_id == $destination->transporter_destination)
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
                        <label class="font-weight-semibold">Truck Status *</label>
                        <input type="text" class="form-control" name="truck_status" id="truckStatus" value="<?php if(isset($recid)) { echo $recid->account_officer; } ?>">
                    </div>
                </div>
            </div>

            <div class="text-left  ml-4 mr-3 mb-3">
                @if(!isset($recid))
                <button type="submit" class="btn btn-primary" name="addTruckAvailability" id="addTruckAvailability">Make Truck Available</button>
                <span id="errorMessage"></span>
                @endif
            </div>

    </div>

    <input type="hidden" name="reported_by" value="{{Auth::user()->id}}">
    <input type="hidden" name="dated" value="{{date('Y-m-d, h:i A')}}">
</form>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/truckAvailability.js')}}"></script>
@stop