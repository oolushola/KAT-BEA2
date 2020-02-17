@extends('layout')

@section('title')Kaya ::. Cargo Availability @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Preference</span> - Cargo Availability</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif Cargo Availability</h5>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label>Current Year: </label>
                    <span class="font-weight-semibold text-primary">{!! date('Y') !!}</span>
                    <label class="ml-5">Current Month:</label>
                    <span class="font-weight-semibold text-primary">{!! date('F') !!}</span>
                </div>
                

                <form method="POST" name="frmCargoAvailability" id="frmCargoAvailability">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                        {!! method_field('PATCH') !!}
                    @endif
                    <div class="form-group">
                        <label>Client Name</label>
                        <select class="form-control" name="client_id" id="clientId">
                            <option value="0">Choose Client</option>
                            @foreach($clients as $client)
                                @if(isset($recid))
                                    @if($recid->client_id == $client->id)
                                    <option value="{{$client->id}}" selected>{{$client->company_name}}</option>
                                    @else
                                    <option value="{{$client->id}}">{{$client->company_name}}</option>
                                    @endif
                                @else
                                <option value="{{$client->id}}">{{$client->company_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="current_year" value="{{date('Y')}}">
                    <input type="hidden" name="current_month" value="{{date('F')}}">
                    <div class="form-group">
                        <label>Cargo Availability</label>
                        <input type="text" class="form-control" placeholder="100" name="available_order" id="availableOrder" value="<?php if(isset($recid)) { echo strtoupper($recid->available_order); } ?>">
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary" id="updateTripRequest">Update 
                        @else
                        <button type="submit" class="btn btn-primary" id="addTripRequest">Add  
                        @endif Available Cargo
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
                <h5 class="card-title">Preview Pane of Available Cargo</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th width="5%">#</th>
                            <th>Period</th>
                            <th>Client Name</th>
                            <th>Available Order</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        @if(count($cargoAvailable))
                            @foreach($cargoAvailable as $cargo)
                            <?php $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                            ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>{{strtoupper($cargo->current_month)}}, {{strtoupper($cargo->current_year)}}</td>
                                <th>{{strtoupper($cargo->company_name)}}</th>
                                <td>{{strtoupper($cargo->available_order)}}</td>
                                <td>
                                        <?php 
                                            $currentMonth = date('F'); 
                                            $currentYear = date('Y');
                                            if($currentMonth == $cargo->current_month && $currentYear == $cargo->current_year){
                                        ?>
                                    <div class="list-icons">
                                        <a href="{{URL('cargo-availability/'.$cargo->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">You've not added ny cargo availability.</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/cargo-availability.js')}}"></script>
@stop
