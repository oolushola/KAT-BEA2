@extends('layout')

@section('title')Kaya::.Pair trucks with drivers @stop

@section('main')

<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Transportation</span> - Truck and Driver Pair</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-truck text-primary"></i> <span>View Trucks</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>View Drivers</span></a>
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
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif Driver and Truck Pairing</h5>
                <i class="icon-stack text-danger-600" id="bulkUpload"></i>
                <i class="icon-stack text-primary-600" id="singleUpload" style="display:none"></i>
            </div>

            <div class="card-body">
                <form method="POST" name="frmTruckDrivers" id="frmTruckDrivers" action="{{URL('upload-bulk-truckanddriverpairing')}}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($recid))
                        <input type="text" name="id" value="{{$recid->id}}" id="id" />
                        {!! method_field('PATCH') !!}
                    @endif

                    <div id="bulkUploadForm" style="display:none">
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="uploadBulkDriverTruck" title="Upload only CSV File"  />

                        </div>
                        <span id="loader1"></span>
                        <button type="submit" class="btn btn-primary" id="uploadBulkRating">Upload 
                                <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>

                    <div id="singleEntryForm">

                        <div class="form-group">
                            <label>Truck No.</label>
                            <select name="truck_id" id="truck_id" class="form-control">
                                <option value="0">Choose Truck</option>
                                @foreach($truckslisting as $truck)
                                    @if(isset($recid) && $recid->truck_id == $truck->id)
                                    <option value="{{$truck->id}}" selected>{{$truck->truck_no}}</option>
                                    @else
                                    <option value="{{$truck->id}}">{{$truck->truck_no}}</option>
                                    @endif

                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Drivers Name & Number</label>
                            <select name="driver_id" id="driver_id" class="form-control">
                                <option value="0">Choose Drivers</option>
                                @foreach($driverslisting as $driver)
                                    @if(isset($recid) && $recid->driver_id == $driver->id)
                                    <option value="{{$driver->id}}" selected>{{$driver->driver_last_name}} {{$driver->driver_first_name}} : {{$driver->driver_phone_number}}</option>
                                    @else
                                    <option value="{{$driver->id}}">{{$driver->driver_last_name}} {{$driver->driver_first_name}} : {{$driver->driver_phone_number}}</option>
                                    @endif
                                @endforeach
                            </select>
                            
                        </div>

                        <div class="text-right">
                            <span id="loader"></span>
                            @if(isset($recid))
                                <button type="submit" class="btn btn-primary" id="updateDriverTruck">Update 
                            @else
                                <button type="submit" class="btn btn-primary" id="saveDriverTruck">Save 
                            @endif
                                Pairing<i class="icon-paperplane ml-2"></i>
                            </button>
                        </div>
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
                <h5 class="card-title">Preview Pane Pairings</h5>
            </div>

            <input type="text" id="myInput" placeholder="Search">

            <div class="table-responsive" style="max-height:600px; overflow:auto">
                <table class="table table-bordered" id="myTable">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Driver</th>
                            <th>Truck</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($driverTruckPairing))
                        @foreach($driverTruckPairing as $pairing)
                        <?php $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                        <tr class="{{$css}}" style="font-size:10px">
                            <td>{{$counter}}</td>
                            <td>{{ucwords($pairing->driver_last_name)}} {{ucwords($pairing->driver_first_name)}}</td>
                            <td>{{$pairing->truck_no}}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('assign-driver-truck/'.$pairing->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="table-success" colspan="6">You've not added any pairing details.</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/assign-driver-truck.js')}}"></script>
@stop