@extends('layout')

@section('title')Kaya::.Invoices @stop

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
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Invoices</span> - Archive</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="{{URL('invoice-multi-search')}}" class="btn btn-link btn-float text-default" target="_blank"><i class="icon-folder-search text-primary"></i><span>Bulk Search</span></a>
                    <a href="{{URL('all-invoiced-trips')}}" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>Invoiced</span></a>
                </div>
            </div>
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="invoice_archive.html" class="breadcrumb-item">Invoices</a>
                    <span class="breadcrumb-item active">Archive</span>
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


    <!-- Content area -->
    <div class="content">

        <!-- Invoice archive -->
        <div class="card">
            <div class="invoiceActionHolder">
                <form method="POST" name="frmClientInvoice" id="frmClientInvoice" action="{{URL('/invoice')}}">
            
                @csrf
                <select name="client_id" id="client" style="padding:10px; margin:5px; width:200px;  height:40px; outline:none">
                    <option value="0">Choose Client</option>
                    @foreach($clientName as $client)
                    <option value="{!! $client->id !!}">{!! ucwords($client->company_name) !!}</option>
                    @endforeach
                </select>
                <input type="text" id="invoiceSearchBox" style="padding:10px; top:30px; width:200px; margin-top:10px;  margin-right:10px; height:40px; outline:none; float:right" placeholder="Quick Search" class="hidden">

                <span id="incentivePlaceholder"></span>
           
                <div class="table-responsive" id="contentLoader">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">TRIP ID</th>
                                <th>CUSTOMER</th>
                                <th>PRODUCT</th>
                                <th>TRUCK NO.</th>
                                <th class="text-center">S.O. Number</th>
                                <th>TONS<sub> in (Kg)</sub></th>
                                <th>AMOUNT</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $totalAmount = 0;
                                $totalVatRate = 0;
                            ?>
                            @if(count($invoiceList))
                                @foreach($invoiceList as $invoice)
                                <?php
                                    $totalAmount+=$invoice->client_rate;
                                    $vatRate = 5 / 100 * $invoice->client_rate;
                                    $totalVatRate+=$vatRate;
                                ?>
                                <tr>
                                    <td class="text-center">{!! $invoice->trip_id !!}</td>
                                    <td>
                                        <h6 class="mb-0">
                                            <a href="#">{!! $invoice->customers_name  !!}</a>
                                            <span class="d-block font-size-sm text-muted">Destination: 
                                                {!! $invoice->state !!}, {!! $invoice->exact_location_id !!}
                                            </span>
                                        </h6>
                                    </td>
                                    <td>{!! $invoice->product !!}</td>
                                    <td><span class="badge badge-primary">{!! $invoice->truck_no !!}</span></td>
                                    <td class="text-center">
                                        @foreach($waybillinfos as $salesOrderNumber)
                                            @if($salesOrderNumber->trip_id == $invoice->id)
                                                {!! $salesOrderNumber->sales_order_no !!}<br>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{!! $invoice->tonnage !!}</td>
                                    <td>
                                        <h6 class="mb-0 font-weight-bold">
                                            &#x20a6;{!! number_format($invoice->client_rate, 2) !!}    
                                            <span class="d-block font-size-sm text-muted font-weight-normal">
                                            VAT: &#x20a6;{!! number_format($vatRate, 2) !!}
                                            </span>
                                        </h6>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">Precise Total Rate and Vat, exclusive of incentive rate</td>
                                    <td style="background:#000; color:#fff">
                                        <h6 class="mb-0 font-weight-bold">
                                            &#x20a6;{!! number_format($totalAmount, 2) !!}    
                                            <span class="d-block font-size-sm text-muted font-weight-normal">
                                            VAT: &#x20a6;{!! number_format($totalVatRate, 2) !!}
                                            </span>
                                        </h6>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="9">No waybill available for invoicing</td>
                                </tr>
                            @endif
                            
                            

                            

                        </tbody>
                    </table>
                </div>
                </form>
            </div>
        
        
        </div>
        <!-- /invoice archive -->

    </div>
    <!-- /content area -->


			

</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/invoice.js')}}"></script>
@stop