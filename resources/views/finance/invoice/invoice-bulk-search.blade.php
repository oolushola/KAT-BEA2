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
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Invoice</span> - Bulk Search Invoice</h4>
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
                    <span class="breadcrumb-item active">Multi Search Invoice</span>
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
                <h5 class="card-title">Waybill </h5>
            </div>

            <div class="card-body">
            
                <form action="" method="POST" name="frmMultipleInvoiceSearch" id="frmMultipleInvoiceSearch" enctype="multipart/form-data">
                    @csrf
                    <span class="error font-weight-semibold" id="addMore" style="cursor:pointer">Add More...</span>
                    

                    <div class="row mb-3 mb-md-2 input_field_wraps">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" class="form-control salesOrderNumber" placeholder="Sales Order Number" name="sales_order_no[]" value="">
                            </div>
                        </div>
                    </div>

                    <div class="text-right" id="defaultButton">
                        <button type="submit" class="btn btn-primary" id="searchInvoiceBank">Search Invoice Bank
                            <i class="icon-search4 ml-2"></i>
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
                <select id="filterByBulkInvoiceStatus">
                    <option value="0">Choose</option>
                    <option value="not invoiced">Not Invoiced</option>
                    <option value="invoiced">Invoiced & Paid</option>
                    <option value="not paid">Invoiced, Not paid</option>
                    <option></option>
                </select>
            </div>

            <div class="table-responsive" id="contentHolder">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Sales Order No.</th>
                            <th>Invoice No.</th>
                            <th>Status</th>                           
                        </tr>
                    </thead>
                   
                </table>
            </div>
        </div>
        <!-- /contextual classes -->


    </div>
</div>

			

</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/invoice.js')}}"></script>
@stop