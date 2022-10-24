@extends('layout')

@section('title') Kaya ::. Internal Truck Verification @stop


@section('css')
    <style>
        input::placeholder {
            font-size:20px
        }
        .form-control {
            font-size: 15px;
            padding: 10px;
        }
    </style>
@stop

@section('main')

    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title font-weight-semibold">TRUCK INTEGRITY CHECK LIST </h6>
        </div>
    </div>
    
    @if(count($isRomOil) > 0) 

    <p class="m-3">Hi, {{ Auth::user()->first_name }}! we have special preference for all trips loaded at ROMOIL, click <a href="{{URL::asset('trips')}}" target="_blank" class="font-weight-bold">HERE</a> to proceed to creating the new trip. </p>

    @endif


    <div class="row">
        <div class="col-md-5 col-sm-6">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="ENTER TRUCK NO" id="truckNo" />
            </div>
        </div>
        <div class="col-md-5 col-sm-6">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="ENTER DRIVER'S NO" id="driversPhoneNum" />
            </div>
        </div>

        <div class="col-md-2 col-sm-12">
            <button class="btn btn-primary mt-0 font-weight-bold" type="button" id="verifyTruckNo" >Proceed with verification</button>
        </div>        
    </div>
    <!--  -->
    <form id="frmTruckDocuments" action="{{URL('upload-truck-documents')}}" method="POST">
        @csrf 
        <div id="contentDropper"></div>
    </form>
        
    


@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/truckAvailability.js?v=').time()}}"></script>
@stop