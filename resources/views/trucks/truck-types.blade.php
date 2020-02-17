@extends('layout')

@section('title')Kaya ::. Truck Types @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Global Operation</span> - Truck Types</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Truck Types</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-truck text-primary"></i> <span>Trucks</span></a>
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
                <h5 class="card-title">
                    @if(isset($recid)) Update @else Add @endif Truck Type
                </h5>
            </div>

            <div class="card-body">
                <form action="" name="frmTruckType" id="frmTruckType">
                    @csrf
                    @if(isset($recid)) {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}"> @endif

                    <div class="form-group">
                        <label>Truck Type Code</label>
                        <input type="text" class="form-control" placeholder="TLV" maxlength="3" name="truck_type_code" id="truckTypeCode" value="<?php if(isset($recid)) { echo $recid->truck_type_code; } ?>" />
                    </div>

                    <div class="form-group">
                        <label>Truck Type</label>
                        <input type="text" class="form-control" placeholder="Fleet231" name="truck_type" id="truckType" value="<?php if(isset($recid)) { echo $recid->truck_type; } ?>" />
                    </div>

                    <div class="form-group">
                        <label>Tonnage</label>
                        <input type="number" class="form-control" placeholder="5000Kg" name="tonnage" id="tonnage" value="<?php if(isset($recid)) { echo $recid->tonnage; } ?>">
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateTruckType">Update Truck Type 
                        @else
                            <button type="submit" class="btn btn-primary" id="addTruckType">Save Truck Type 
                        @endif
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
                <h5 class="card-title">Preview Pane of Truck Types</h5>
            </div>
            <div>{!! $trucktypes->links() !!}</div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Truck Type Code</th>
                            <th>Truck Type</th>
                            <th>Tonnage <sub>(Kg)</sub></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($trucktypes))
                        @foreach($trucktypes as $trucktype)
                        <?php
                            $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>{{strtoupper($trucktype->truck_type_code)}}</td>
                                <td>{{$trucktype->truck_type}}</td>
                                <td>{{$trucktype->tonnage}}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('truck-types/'.$trucktype->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    @else
                        <tr class="table-info">
                            <td colspan="6"></td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/truck-type.js')}}"></script>
@stop
