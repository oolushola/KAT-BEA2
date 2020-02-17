@extends('layout')

@section('title')Kaya::.Add Incentives @stop

@section('main')

<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Invoice</span> - Invoice Incentive</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="{{URL('invoices')}}" class="btn btn-link btn-float text-default"><i class="icon-folder-search text-primary"></i><span>Invoice Archive</span></a>
                    <a href="{{URL('all-invoiced-trips')}}" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>Invoiced</span></a>
                </div>
            </div>
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="{{URL('invoices')}}" class="breadcrumb-item">Invoices</a>
                    <span class="breadcrumb-item active">Incentive</span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="breadcrumb justify-content-center">
                    <a href="#" class="breadcrumb-elements-item">
                        <i class="icon-comment-discussion mr-2"></i>
                        Escalate
                    </a>

                </div>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <div class="row">
        <div class="col-md-5">
        &nbsp;

            <!-- Basic layout-->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Incentives </h5>
                </div>

                <div class="card-body">
                
                    <form method="POST" id="frmIncentive">
                        @csrf

                        @if(isset($recid))
                            {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                        @endif

                        <div class="row mb-3 mb-md-2">
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>State</label>
                                    <select class="form-control" name="state" id="state">
                                        <option value="">Choose State</option>
                                        @foreach($states as $state)
                                        @if(isset($recid) && $recid->state == $state->regional_state_id)
                                        <option value="{{$state->regional_state_id}}" selected>{{$state->state}}</option>
                                        @else
                                        <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="exactLocationPlaceholder">
                                    <label>Exact Location</label>
                                    <select class="form-control" name="exact_location_id" id="exact_location">
                                        <option value="">Choose Destination</option>
                                        @if(isset($recid)){
                                            @foreach($alldestionationsPerstate as $exactLocation)
                                                @if($exactLocation->transporter_destination == $recid->exact_location)
                                                <option value="{{$exactLocation->transporter_destination}}" selected>{{$exactLocation->transporter_destination}}</option>
                                                @else
                                                <option value="{{$exactLocation->transporter_destination}}">{{$exactLocation->transporter_destination}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                        }
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Incentive Description" name="incentive_description" id="incentiveDescription" value="@if(isset($recid)){{$recid->incentive_description}}@endif">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Amount" name="amount" id="amount" value="@if(isset($recid)){{$recid->amount}}@endif">
                                </div>
                            </div>
                        </div>

                        <div id="responsePlace"></div>

                        <div class="text-right" id="defaultButton">
                            <button type="submit" class="btn btn-primary" id="@if(isset($recid)){{'updateIncentives'}}@else {{'addIncentives'}}@endif">@if(isset($recid))Update @else Add @endif Incentives
                                <i class="icon-coins ml-2"></i>
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
                    <h5 class="card-title">Preview Pane</h5>
                </div>

                <div class="table-responsive" id="contentHolder">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr style="font-size:11px;">
                                <th>#</th>
                                <th>State</th>
                                <th>Destionation</th>
                                <th>Incentive Description</th>
                                <th>Incured Cost (&#x20a6;)</th> 
                                <th>Action</th>                         
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($incentiveLists))
                                <?php $count = 0; ?>
                                    @foreach($incentiveLists as $incentiveOnDestination)
                                        <?php 
                                            $count++; 
                                            $count % 2 == 0 ? $css = '' : $css = 'table-success'; 
                                        ?>
                                        <tr class="{{$css}}">
                                            <td>{{$count}}</td>
                                            <td>{{$incentiveOnDestination->state}}</td>
                                            <td>{{$incentiveOnDestination->exact_location}}</td>
                                            <td>{{$incentiveOnDestination->incentive_description}}</td>
                                            <td>{{number_format($incentiveOnDestination->amount, 2)}}</td>
                                            <td>
                                                <a href="{{URL('incentives/'.$incentiveOnDestination->id.'/edit')}}">
                                                    <i class="icon-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                            @else
                                <tr>
                                    <td>You did not add any incentive for this location</td>
                                </tr>
                            @endif
                        </tbody>
                    
                    </table>
                </div>
            </div>
            <!-- /contextual classes -->


        </div>
    </div>

</div>


@stop

@section('script')
<script type="text/javascript" src="{{URL('/js/validator/incentives.js')}}"></script>
@stop

