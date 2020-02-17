@extends('layout')

@section('title')Kaya ::. Trucks @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Transportation</span> - Trucks</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Trucks</span></a>
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
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif Truck</h5>
            </div>

            <div class="card-body">
                <form method="POST" name="frmTrucks" id="frmTrucks">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                        {!! method_field('PATCH') !!}
                    @endif
                    <div class="form-group">
                        <label>Transporter</label>
                        <select class="form-control" name="transporter_id" id="transporterId">
                            <option value="0">Choose Transporter</option>
                            @foreach($transporters as $transporter)
                                @if(isset($recid))
                                    @if($recid->transporter_id == $transporter->id)
                                    <option value="{{$transporter->id}}" selected>{{$transporter->transporter_name}}</option>
                                    @else
                                    <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
                                    @endif
                                @else
                                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Truck Type</label>
                        <select class="form-control" name="truck_type_id" id="truckTypeId">
                            <option value="0">Choose Truck Type</option>
                            @foreach($truckTypes as $truckType)
                                @if(isset($recid))
                                    @if($recid->truck_type_id == $truckType->id)
                                    <option value="{{$truckType->id}}" selected>{{$truckType->truck_type}}:{{$truckType->tonnage}}kg</option>
                                    @else
                                    <option value="{{$truckType->id}}">{{$truckType->truck_type}}:{{$truckType->tonnage}}kg</option>
                                    @endif
                                @else
                                <option value="{{$truckType->id}}">{{$truckType->truck_type}}:{{$truckType->tonnage}}kg</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Truck No</label>
                        <input type="text" class="form-control" placeholder="IKD 445 LD" name="truck_no" id="truckNumber" value="<?php if(isset($recid)) { echo strtoupper($recid->truck_no); } ?>">
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary" id="updateTruck">Update 
                        @else
                        <button type="submit" class="btn btn-primary" id="addTruck">Add  
                        @endif Truck
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
                <h5 class="card-title">Preview Pane of Trucks</h5>
                <p style="font-size:10px;">{{$trucks->links()}}</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th width="5%">#</th>
                            <th>Transporter</th>
                            <th>Truck No</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        @if(count($trucks))
                            @foreach($trucks as $truck)
                            <?php $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                            ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>
                                    @foreach($transporters as $transporter)
                                        @if($transporter->id == $truck->transporter_id)
                                            {{$transporter->transporter_name}}
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{strtoupper($truck->truck_no)}}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('trucks/'.$truck->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">You've not added any trucks.</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/truck.js')}}"></script>
@stop
