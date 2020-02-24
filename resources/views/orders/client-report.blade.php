@extends('layout')

@section('title') Kaya ::. Client Reports @stop

@section('css')
<style type="text/css">
textarea:focus, input:focus, select:focus{
    outline: none;
}
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>
@stop

@section('main')
<div class="card">
    <form method="post" id="frmCompleteClientReport">
        @csrf
        <div id="contentLoader"></div>
        <div class="card-header header-elements-inline">
            <select id="clientReportId" style="width:100%; height:40px; font-size:13px; border:1px solid #000; border:0; background:none">
                <option>Choose Client</option>
                @foreach($clientlistings as $client)
                <option value="{{$client->id}}">{{ucwords($client->company_name)}}</option>
                @endforeach
            </select>
        </div>

        <div class="table-responsive" id="contentPlaceholder">
            <div class="card-header header-elements-inline">
                <h5 class="card-title"><button class="btn btn-primary"><i class="icon icon-file-download"></i> Download Report</button></h5>
            </div>
            <table class="table table-bordered">
                <thead class="table-info font-weigth-semibold" style="font-size:10px;">
                    <tr>
                        <th>#</th>
                        <th>Loading Site</th>
                        <th class="text-center">Sales Order No.</th>
                        <th class="text-center">Vehicle No.</td>
                        <th>Customer</th>
                        <th>Destination</th>
                        <th>Product</th>
                        <th class="text-center">Gate In</th>
                        <th>Time since gate in</th>
                        <th class="text-center">Arrival at loading bay</th>
                        <th>loading bay departure time</th>
                        <th class="text-center">Gate out</th>
                        <th>Last known location</th>
                        <th>Latest time</th>
                        <th class="text-center">Time arrived destination</th>
                        <th class="text-center">Offloading Duration</th>
                        <th class="text-center">Current Stage</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                
            </table>
        </div>
    </form>
    
</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('/js/validator/excelme.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('/js/validator/sort.js')}}"></script>
@stop