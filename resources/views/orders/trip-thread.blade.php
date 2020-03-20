@extends('layout')

@section('title')Kaya ::. View Thread @stop
@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>
@stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">TRIP THREAD VIEW</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span></span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>View Client Specific History</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">TRIPS</h5>
            </div>
            <div class="col-md-12 mb-2">{{ $pagination->links() }}</div>


            <div class="card-body row">
                @if(count($pagination))
                    <?php $count = 0; ?>
                    @foreach($pagination as $trip)
                    <?php $count++; $count % 2 == 0 ? $class="bg-primary-300" : $class=""; ?>
                    <div class="col-md-2 mb-2 hover specificTripEvent" id="{{ $trip->id }}" >

                        <h5 class="text-primary">{{$trip->trip_id}}</h5> <span style="display:inline-block; font-weight:bold">{{$trip->truck_no}}</span>
                        <p style=" display:inline-block; font-weight:bold"  class="text-danger">
                            Gated Out: 
                            @if(isset($trip->gated_out))
                            {{date('d-m-Y', strtotime($trip->gated_out))}}
                            @else
                            NA
                            @endif
                        </p>
                    </h5>

                    </div>
                    @endforeach
                @else

                @endif
            </div>
        </div>
        <!-- /basic layout -->
        <input type="hidden" name="page">
        
        

    </div>

    <div class="col-md-4">
    &nbsp;

        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">TRIP LOG</h5>
            </div>

            <div class="row" >
                <div id="contentPlaceholder" class="col-md-12" style="max-height:1700px; overflow:auto"></div>
            </div>
            
        </div>
        <!-- /contextual classes -->


    </div>
</div>

@stop

@section('script')
<script src="{{URL::asset('js/validator/trip-event.js')}}"></script>
@stop
