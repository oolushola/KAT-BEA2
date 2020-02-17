@extends('layout')

@section('title')Kaya ::. Products @stop
@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Settings</span> - Invoice Header</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Products</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Invoicing Subheadings</span></a>
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
                <h5 class="card-title">Add Subheading</h5>
            </div>

            <div class="card-body">
                <form id="frmSubheading" method="POST" action="">
                    @csrf 
                    
                    @if(isset($recid)) {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}"> @endif
                    <div class="form-group">
                        <label>Choose Client</label>
                       <select class="form-control" name="client_id" id="clientId">
                           <option value="">Choose Client</option>
                           @foreach($clients as $client)
                            <option value="{{$client->id}}">{{ucwords($client->company_name)}}</option>
                           @endforeach
                       </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Sales Order No." name="sales_order_no_header" id="salesOrderNoHeader" value="<?php if(isset($recid)){ echo $recid->sales_order_no_header; }  ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Waybill | Invoice No." name="invoice_no_header" id="invoiceNumberHeader" value="<?php if(isset($recid)){ echo $recid-> invoice_no_header; }  ?>">
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateInvoiceHeading">Update Invoice Heading 
                        @else
                            <button type="submit" class="btn btn-primary" id="addInvoiceHeading" >Save Invoice Heading 
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
                <h5 class="card-title">Preview Pane of Invoice Subheading</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Client Name</th>
                            <th>What I want to use?</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count=0; ?>
                        @if(count($invoiceHeadings))
                            @foreach($invoiceHeadings as $invoiceHead)
                            <?php $count+=1; ?>
                            <tr>
                                <td>{{$count}}</td>
                                <td>{{$invoiceHead->company_name}}</td>
                                <td>{{$invoiceHead->sales_order_no_header}} - {{$invoiceHead->invoice_no_header}}</td>
                                <td>
                                    <a href="{{URL('invoice-subheading/'.$invoiceHead->id.'/edit')}}"><i class="icon-pencil"></i></a>&nbsp;
                                    <a href=""><i class="icon-trash text-danger-400"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="4">You've not added any subheading rule</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/invoice-subheading.js')}}"></script>
@stop
