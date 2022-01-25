@extends('layout')

@section('title') Follow with ::. ABC 123 XY @stop

@section('main')

<div class="page-header page-header-light">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Follow Up</span> - Truck No</h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>

    <div class="header-elements d-none">
      <div class="d-flex justify-content-center">
        <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View All Chases</span></a>
        <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>To be determined...</span></a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <form action="" method="POST" id="frmTripEvent" name="frmTripEvent">
      @csrf
      @if(isset($recid))
        <input type="hidden" name="id" id="id" value="{{$recid->id}}" >
        {!! method_field('PATCH') !!} 
      @endif
      <!-- Basic layout-->
      <div class="card">
        <div class="card-body">
            <div id="journeyContainer">
                <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <input type="text" class="form-control" name="location_check_one" id="morningVisibility" style="border-radius:0px;" value="<?php if(isset($recid)) {echo $recid->location_check_one; } ?>" placeholder="Truck No">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <input type="text" class="form-control" name="location_check_two" id="" style="border-radius:0px;" value="" placeholder="Transporter">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <input type="text" class="form-control" name="" id="" style="border-radius:0px;" value="" placeholder="Driver">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Expected Date of Availability</label>
                        <input type="date" class="form-control" name="" id="" style="border-radius:0px;" value="">
                      </div>
                    </div>
                  
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Preffered Loading Site</label>
                        <select class="form-control" name="" id="" style="border-radius:0px;" value="">
                          <option value="">Choose a preffered loading site</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Preffered Destination</label>
                        <input type="text" class="form-control" name="" id="" style="border-radius:0px;" value="">
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea class="form-control" name="" id="" style="border-radius:0px;" placeholder="Remarks"></textarea>
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
        </tbody>
    </table>
  </div>
</div>

@stop