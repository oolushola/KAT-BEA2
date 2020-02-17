@extends('layout')

@section('title')Kaya::.Local Purchase Order @stop

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
<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Local Purchase Order</span> - Archive</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-link btn-float text-default"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
                    <a href="{{URL('view-orders')}}" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View-trips</span></a>
                </div>
            </div>
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="invoice_archive.html" class="breadcrumb-item">Local Purchase Order</a>
                    <span class="breadcrumb-item active">Archive</span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <!-- Content area -->
    <div class="content">

        <!-- Invoice archive -->
        <div class="card">
            <form method="POST" name="frmClientInvoice" id="frmClientInvoice">
                @csrf
                <div class="table-responsive" id="contentLoader">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th class="text-center">TRIP ID</th>
                                <th>TRANSPORTER</th>
                                <th>CUSTOMER</th>
                                <th>PRODUCT</th>
                                <th class="text-center">TRUCK NO.</th>
                                <th class="text-center">S.O. NUMBER</th>
                                <th class="text-center">AMOUNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0; ?>
                            @if(count($lposummary))
                                @foreach($lposummary as $lpo)
                                <?php 
                                    $count++;
                                
                                ?>
                                
                                    <tr class="hover" style="font-size:11px; cursor:pointer" onclick="window.location='local-purchase-order/{{$lpo->trip_id}}'">
                                        <td class="text-center">{!! $lpo->trip_id !!}</td>
                                        <td>{!! $lpo->transporter_name !!}</td>
                                        <td>{!! $lpo->customers_name !!}</td>
                                        <td>{!! $lpo->product !!}</td>
                                        <td class="text-center">{!! $lpo->truck_no !!}</td>
                                        <td class="text-center">
                                            @foreach($waybillinfos as $salesOrderNumber)
                                                @if($salesOrderNumber->trip_id == $lpo->id)
                                                    {!! $salesOrderNumber->sales_order_no !!}<br>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="text-center">&#x20a6;{!! number_format($lpo->transporter_rate, 2) !!}</td>
                                    </tr>
                                </a>
                                @endforeach
                            @else

                            @endif
                        </tbody>
                    </table>
                </div>
            </form>
        
        
        </div>
        <!-- /invoice archive -->

    </div>
    <!-- /content area -->


			

</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/invoice.js')}}"></script>
@stop