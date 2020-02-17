@extends('layout')

@section('title') Kaya ::. Initial Trip Order @stop

@section('css')
<style type="text/css">
table > thead{
    font-size:11px;;
    font-weight:bold;
    background:#eee; 
    text-transform:uppercase;
    text-align:center;
}

table > tbody{
    font-size:11px;
    text-align:center
}
</style>
@stop

@section('main')

<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title font-weight-semibold">Initial Trip Confirmation</h6>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card ">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title font-weight-semibold">Transporter Checks & Balance</h6>
            </div>
            <div class="row ml-3 mr-3 mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-semibold">Transporter</label>
                        <input type="text" id="searchTransporters" class="form-control" placeholder="Search Transporter" >
                        <p class="text-danger font-weight-semibold" style="font-size:10px; padding-top:10px; ">Can't find transporter? <a class="text-primary" href="#transporterQuickAdd" data-toggle="modal">CLICK HERE</a> to add </p>
                    </div>
                    <div class="table-responsive hidden" id="transporterList">
                        <table class="table table-stripped" id="transporterBank">
                            <thead>
                                <tr>
                                    <th>Transporter Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 0; ?>
                                @if(count($transporters))
                                    @foreach($transporters as $transporter)
                                    <?php $count+=1; 
                                        $count % 2 == 0 ? $css_style = 'table-success' : $css_style='';
                                    ?>
                                        <tr>
                                            <td>{!! $transporter->transporter_name !!}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>Sorry, We can't find any Transporter</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-semibold">Trucks</label>
                        <input type="text" id="searchTrucks" class="form-control" placeholder="Search Trucks" >

                        <p class="text-danger font-weight-semibold" style="font-size:10px; padding-top:10px; ">Can't find truck? <a class="text-primary" href="#truckQuickAdd" data-toggle="modal">CLICK HERE</a> to add </p>
                    </div>

                    <div class="table-responsive hidden" id="truckList">
                        <table class="table table-stripped" id="truckBank">
                            <thead>
                                <tr>
                                    <th>TRUCK NO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($truckBank))
                                <?php $iterator = 0; ?>
                                    @foreach($truckBank as $truck)
                                        <?php $iterator+=1; 
                                            $iterator % 2 == 0 ? $css_style = 'table-success' : $css_style='';
                                        ?>
                                        <tr>
                                            <td>{{$truck->truck_no}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    no record available
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            

        </div>
    </div>

    <div class="col-md-4">
        <div class="card ">
            <div class="card-header bg-white header-elements-inline">
                <h6 class="card-title font-weight-semibold">Drivers Check & Balance</h6>
            </div>
            <form role="form" name="" id="">
                <div class="row ml-3 mr-3 mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-semibold">Driver's Fullname</label>
                            <input type="text" id="searchDrivers" class="form-control" placeholder="Search Drivers ">
                            <p class="text-danger font-weight-semibold" style="font-size:10px; padding-top:10px; ">Can't find driver details? <a class="text-primary" href="#driverQuickAdd" data-toggle="modal">CLICK HERE</a> to add </p>
                        </div>
                    </div>
                    <div class="table-responsive hidden" id="driversList">
                        <table class="table table-stripped" id="driverBank">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Licence No.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 0; ?>
                                @if(count($driversBank))
                                    @foreach($driversBank as $driver)
                                    <?php $counter+=1; 
                                        $counter % 2 == 0 ? $css_style = 'table-success' : $css_style='';
                                    ?>
                                        <tr>
                                            <td>{!! ucwords($driver->driver_first_name) !!}
                                                {!! ucwords($driver->driver_last_name) !!}
                                            </td>
                                            <td>{!! $driver->licence_no !!}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    no record found.
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<div class="text-left  ml-3 mr-3 mb-3">
    <button type="submit" class="btn btn-primary" onclick="window.location='/trips'">Proceed<i class="icon-arrow-right7 ml-2"></i></button>
</div>

<!-- TRANSPORTER POP UP FORM -->
@include('orders.partials._transporter-quick-add')


<!-- TRUCK POP UP FORM -->
@include('orders.partials._truck-quick-add')


<!-- Driver POP UP FORM -->
@include('orders.partials._driver-quick-add')


@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('/js/validator/initial-requirement.js')}}"></script>
@stop