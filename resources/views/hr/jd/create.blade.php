@extends('layout')
@section('title')Performance Review Activity @stop
@section('main')


    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">{{'Job Description'}}</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>REVIEW</span></a>
                    <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>ECDP</span></a>
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
                            
                        </h5>
                    </div>

                    <div class="card-body">
                        

                        <div id="journeyContainer">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Main Duty </label>
                                        <input type="text" class="form-control" name="main_duty" id="mainDuty" style="border-radius:5px;" placeholder="Bookeeping">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>How often do you do this?</label>
                                        <select type="text" class="form-control" name="frequency" id="frequency" style="border-radius:5px;">
                                            <option value="">Choose Frequency</option>
                                            <option value="Daily">Daily</option>
                                            <option value="Weekly">Weekly</option>
                                            <option value="Monthly">Monthly</option>
                                            <option value="Yearly">Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Description(What does it mean?)</label>
                                        <textarea type="text" class="form-control" name="location_check_one" id="morningVisibility" style="border-radius:5px;" placeholder="To record accounts receivables data."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Objective(Why are you doing it?)</label>
                                        <textarea type="text" class="form-control" name="location_check_one" id="morningVisibility" style="border-radius:5px;" placeholder="TDeliver reports electronically"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Expected Result</label>
                                        <textarea type="text" class="form-control" name="location_check_one" id="morningVisibility" style="border-radius:5px;" placeholder="All financial data captured accurately and timely"></textarea>
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
                    <h5 class="card-title">Job Description Log </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr style="font-size:11px;">
                                <th>Ref</th>
                                <th>Main Duties</th>
                                <th>Frequency</th>
                                <th>Description</th>
                                <th>Objective</th>
                                <th colspan="2">Standard/Output</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /contextual classes -->
        </div>

        
    </div>


@stop

@section('script')

@stop


