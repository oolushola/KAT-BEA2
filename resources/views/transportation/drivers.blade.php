@extends('layout')

@section('title')Kaya ::. Drivers @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Transportation</span> - Drivers</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-truck text-primary"></i> <span>View Trucks</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Truck History</span></a>
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
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif New Driver</h5>
                <i class="icon-stack text-danger-600" id="bulkUpload"></i>
                <i class="icon-stack text-primary-600" id="singleUpload" style="display:none"></i>
            </div>

            <div class="card-body">
                <form method="POST" name="frmDrivers" id="frmDrivers" action="{{URL('upload-bulk-drivers')}}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" value="{{$recid->id}}" id="id" />
                        {!! method_field('PATCH') !!}
                    @endif

                    <div id="bulkUploadForm" style="display:none">
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="uploadBulkDrivers" title="Upload only CSV File"  />

                        </div>
                        <span id="loader1"></span>
                        <button type="submit" class="btn btn-primary" id="uploadBulkRating">Upload Bulk Rates 
                                <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>

                    <div id="singleEntryForm">

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" placeholder="John" name="driver_first_name" id="driversFirstName" value="<?php if(isset($recid)) { echo $recid->driver_first_name; } ?>" >
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" placeholder="Doe" name="driver_last_name" id="driversLastName" value="<?php if(isset($recid)) { echo $recid->driver_last_name; } ?>" >
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" placeholder="+234-***-***-****" name="driver_phone_number" id="driversPhoneNumber" value="<?php if(isset($recid)) { echo $recid->driver_phone_number; } ?>">
                        </div>

                        <legend class="font-weight-semibold"><i class="icon-people mr-2"></i> Motor Boy Information</legend>

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" placeholder="John" name="motor_boy_first_name" id="motorBoyFirstName" value="<?php if(isset($recid)) { echo $recid->motor_boy_first_name; } ?>">
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" placeholder="Doe" name="motor_boy_last_name" id="motorBoyLastName" value="<?php if(isset($recid)) { echo $recid->motor_boy_last_name; } ?>">
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" placeholder="+234-***-***-****" name="motor_boy_phone_no" id="motorBoyPhoneNumber" value="<?php if(isset($recid)) { echo $recid->motor_boy_phone_no; } ?>">
                        </div>

                        <div class="text-right">
                            <span id="loader"></span>
                            @if(isset($recid))
                                <button type="submit" class="btn btn-primary" id="updateDriverDetails">Update 
                            @else
                                <button type="submit" class="btn btn-primary" id="saveDriverDetails">Save 
                            @endif
                                Driver's Detail <i class="icon-paperplane ml-2"></i>
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
                <h5 class="card-title">Preview Pane of Drivers</h5>
            </div>

            <input type="text" id="myInput" placeholder="Search">

            <div class="table-responsive" style="max-height:600px; overflow:auto">
                <table class="table table-bordered" id="myTable">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Driver</th>
                            <th>Phone No.</th>
                            <th>Motor Boy<sub></th>
                            <th>Phone No.</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($drivers))
                        @foreach($drivers as $driver)
                        <?php $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                        <tr class="{{$css}}" style="font-size:10px">
                            <td>{{$counter}}</td>
                            <td>{{ucwords($driver->driver_last_name)}} {{ucwords($driver->driver_first_name)}}</td>
                            <td>{{$driver->driver_phone_number}}</td>
                            <td>{{ucwords($driver->motor_boy_last_name)}} {{ucwords($driver->motor_boy_first_name)}}</td>
                            <td>{{$driver->motor_boy_phone_no}}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('drivers/'.$driver->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="table-success" colspan="6">You've not added any driver details.</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/driver.js')}}"></script>
@stop
