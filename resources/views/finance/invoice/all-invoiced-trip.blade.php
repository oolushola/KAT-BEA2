@extends('layout')

@section('title')Kaya::All Invoiced Trips @stop

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
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Invoice</span> - Invoiced Trips</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-link btn-float text-default"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
                    <a href="{{URL('invoices')}}" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>Available for Invoice</span></a>
                </div>
            </div>
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="invoice_archive.html" class="breadcrumb-item">Invoices</a>
                    <span class="breadcrumb-item active">Invoiced Trips</span>
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
            <form method="POST" name="frmPaidInvoices" id="frmPaidInvoices" action="{{URL('paid-invoices')}}">
                @csrf
                @if(count($errors))
                    <ul class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="table-responsive" id="contentLoader">
                    <table class="table table-striped">
                        <thead>
                            <tr class="font-size-sm font-weight-bold">
                                <th>#</th>
                                <th>Client Name</th>
                                <th class="text-center">Invoice Number</th>
                                <th class="text-center"><button type="button" class="btn btn-success" id="acknowledgedInvoices">Acknowledged</button></th>
                                <th class="text-center">Date Acknowledge</th>
                                <th class="text-center">
                                    <button type="submit" class="btn btn-primary" id="paidInvoices">Paid?</button>
                                    <span id="loader"></span>
                                </th>
                                
                                <th>Date Paid</th>
                                <th class="text-center">Payment Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="font-size-sm font-weight-bold" style="font-size:10px;">
                            <?php $count = 0;  ?>

                            @if(count($completedInvoice))
                                @foreach($completedInvoice as $completeInvoice)
                                <?php 
                                    $count++;
                                    $now=time(); 
                                    $date = strtotime($completeInvoice->acknowledged_date); 
                                    $datediff = $now - $date;
                                    
                                    $noofdayscknowledge = round($datediff/(60 * 60 * 24));
                                    
                                    
                                    $checker = $completeInvoice->acknowledged_date;
                                    $class = 'badge-primary';
                                    $description = 'pending';
                                    $checkStatus = '';
                                    $disabledStatus = '';
                                    if($checker == ''){
                                        $class = 'badge-primary';
                                        $description = 'pending';
                                        $checkStatus = '';
                                        $disabledStatus = '';
                                    }
                                    elseif($checker && $noofdayscknowledge >= 14){
                                        $class = 'badge-danger';
                                        $description = 'Overdue';
                                        $checkStatus = 'a';
                                        $disabledStatus = '';
                                    }
                                    if($completeInvoice->date_paid != ''){
                                        $class ='badge-success';
                                        $description = 'Paid';
                                        $checkStatus = 'checked';
                                        $disabledStatus = 'disabled';
                                    }
                                    

                                    if($completeInvoice->acknowledged == TRUE) {
                                        $acknowledgeClass = 'bg-success';
                                        $acknowledgementStatus = '<span class="icon-checkmark2" title="Received"></span>';
                                    } else {
                                        $acknowledgeClass = '';
                                        $acknowledgementStatus = '<input type="checkbox" name="acknowledgedInvoice[]" class="acknowledgment">
                                            <div class="hidden" style="font-size:10px; font-weight:bold; outline:none">
                                                <div>Date Acknowledge</div>
                                                <input type="date" name="acknowledgmentDate[]" style="outline:none" >
                                                <input type="hidden" name="acknowledgedInvoiceId[]" value="'.$completeInvoice->invoice_no.'">
                                            </div>
                                        ';
                                    }
                                
                                ?>
                                <tr class>
                                    <td>{{$count}} <input type="hidden" name="paid_invoices[]" value="{!! $completeInvoice->invoice_no !!}"></td>
                                    <td>{{strtoupper($completeInvoice->company_name)}}</td>
                                    
                                    <td class="text-center {{$acknowledgeClass}}">{!! $completeInvoice->completed_invoice_no !!}</td>
                                    <td class="text-center">{!! $acknowledgementStatus !!}</td>
                                    <td class="text-center">
                                        @if($completeInvoice->acknowledged == true)
                                            {{date('d/m/Y', strtotime($completeInvoice->acknowledged_date))}}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($completeInvoice->acknowledged == false)
                                        <input type="checkbox" disabled >
                                        @else
                                        <input type="checkbox" {{ $checkStatus }} {{ $disabledStatus }} class="paymentCriteria" >
                                        @endif
                                        <div class="hidden" style="font-size:10px; font-weight:bold; outline:none">
                                            <div>Date Paid</div>
                                            <input type="date" name="paymentDate[]" value="{{$completeInvoice->invoice_no}}" style="outline:none" >
                                        </div>

                                    </td>
                                    <td>
                                        @if($completeInvoice->paid_status == true)
                                            {{date('d/m/Y', strtotime($completeInvoice->date_paid))}}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{$class}}">{{$description}}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{URL('invoice-trip/'.$completeInvoice->completed_invoice_no)}}" target="_blank">
                                            <span class="icon-eye"></span>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            @else

                            @endif
                        </tbody>
                    </table>
                </div>
                <input type="hidden" value="" name="acknowledgeChecker" id="acknowledgeChecker">
            </form>
        
        
        </div>
        <!-- /invoice archive -->

    </div>
    <!-- /content area -->


			

</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('/js/validator/invoice.js')}}"></script>

@stop