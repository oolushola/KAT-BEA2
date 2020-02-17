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
                
                    <form action="{{URL('store-incentives')}}" method="POST" name="frmAddIncentives" id="frmAddIncentives">
                        @csrf
                        <div class="row mb-3 mb-md-2">
                            @foreach($tripids as $kayaTripId)
                            <span class="col-md-12">{{$kayaTripId->trip_id}}</span>
                            <input type="hidden" name="trip_id[]" value="{{$kayaTripId->id}}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Incentive Description" name="incentive_description[]" value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control salesOrderNumber" placeholder="Amount" name="incentive_amount[]" value="">
                                </div>
                            </div>
                            @endforeach
                            
                        </div>

                        <div class="text-right" id="defaultButton">
                            <button type="submit" class="btn btn-primary" id="addIncentives">Add Incentives
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
                                <th>TRIP ID</th>
                                <th>Incentive Description</th>
                                <th>Incured Cost (&#x20a6;)</th>                         
                            </tr>
                        </thead>
                        <tbody>
                            @if($incentivePerTrips)
                                <?php $count = 0; ?>
                                    @foreach($incentivePerTrips as $incentiveOnTrip)
                                        @foreach($tripids as $tripId)
                                            <?php $trip_id = intval(str_replace('KAID', '', $tripId->trip_id)) ?>
                                        @if($incentiveOnTrip->trip_id == $trip_id)
                                    
                                        <?php $count++; 
                                            $count % 2 == 0 ? $css = '' : $css = 'table-success'; 
                                        ?>
                                        <tr class="{{$css}}">
                                            <td>{{$count}}</td>
                                            <td>{{$tripId->trip_id}}</td>
                                            <td>{{$incentiveOnTrip->incentive_description}}</td>
                                            <td>{{number_format($incentiveOnTrip->amount, 2)}}</td>
                                        </tr>
                                        @break;
                                        @endif
                                            @continue
                                        @endforeach
                                        
                                    @endforeach
                               
                            @else
                                <tr>
                                    <td>You did not add any incentive for this trip</td>
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

