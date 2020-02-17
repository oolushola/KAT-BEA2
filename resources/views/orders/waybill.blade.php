@extends('layout')

@section('title')Kaya ::. Waybill status for {{$orderId}} at {{$client_name}} @stop
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

<?php $auth = Auth::user()->role_id; ?>

<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i>
                @if($tracker <= 4)
                <a href="{{URL('trips/'.$tripId.'/edit')}}">Proceed to Gate Out Information</a>
                @elseif($tracker >= 5)
                <a href="{{URL('view-orders')}}">View Orders</a>
                @endif
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span class="font-weight-semibold">{{$orderId}}</span></a>
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
                <h5 class="card-title">Waybill </h5>
            </div>

            <div class="card-body">
                @if(isset($recid))
                <form action="{{URL('way-bill', $recid->id)}}" method="POST" name="frmWayBill" id="frmWayBill" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                    {!! method_field('PATCH') !!}
                @else
                <form action="{{URL('way-bill')}}" method="POST" name="frmWayBill" id="frmWayBill">
                @endif
                    <input type="hidden" name="trip_id" id="tripId" value="{{$tripId}}" >
                    <input type="hidden" name="tracker" id="tracker" value="{{$tracker}}" >
                    <input type="hidden" name="waybill_name" value="{{$orderId}}{{$client_name}}">
                @csrf
                                        
                    @if(!isset($recid))
                    <span class="error font-weight-semibold" id="addMore" style="cursor:pointer">Add More...</span>
                    @endif

                    <div class="row mb-3 mb-md-2 input_field_wraps">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control salesOrderNumber" placeholder="Sales Order Number" name="sales_order_no[]" value="@if(isset($recid)){{$recid->sales_order_no}} @endif">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Invoice Number" name="invoice_no[]" value="@if(isset($recid)){{$recid->invoice_no}}@endif" >
                            </div>
                        </div>
                    </div>

                    @if(isset($recid))
                        <div class="form-group" id="wayBillContainer">
                            <label>Upload Waybill @if(isset($recid) && ($recid->photo == 1))<i class="icon-rotate-cw2"></i>@endif</label>
                            <input type="file" name="photo" id="file" @if(isset($recid) && ($recid->photo == 1))disabled @endif>
                            <input type="hidden" name="filecheck" id="filecheck" value="0" /> 
                            <input type="hidden" name="ftype" id="ftype" value="png,jpg,jpeg,svg,gif" />
                        </div>
                    @endif
                    
                    @if($auth == 1 || $auth == 2 || $auth == 3 || $auth == 4)
                        <legend class="font-weight-semibold"><i class="icon-comment-discussion mr-2"></i> Comment about this waybill</legend>

                        <div class="form-group">
                            <label class="text-primary">Waybill received yet?</label><br>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input waybillStatus" name="tripwaybillstatus" value="1">Yes
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input waybillStatus" name="tripwaybillstatus" value="0">No
                                </label>
                            </div>
                            <input type="hidden" name="waybill_status" id="statusChecker" value="" >
                        </div>

                        <div class="form-group hidden" id="withHolderContainer">
                            <label>Waybill Status</label>
                            <input type="text" class="form-control" name="comment" id="comment" placeholder="Who is with the waybill?">
                        </div>
                        <div class="hidden" id="waybillRemark">
                            <span id="loader2"></span>
                            <button type="submit" class="btn btn-primary" id="addWaybillRemark">Update Waybill Remark</button>
                        </div>
                    @endif

                    <div class="text-right" id="defaultButton">
                        <span id="loader">@include('errors')</span>
                        @if(isset($recid)) 
                        <button type="submit" class="btn btn-primary" id="updateWayBillStatus">Update Waybill 
                        @else
                        <button type="submit" class="btn btn-primary" id="addWaybillStatus">Add Waybill
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
                <h5 class="card-title">Waybill Status for trip : {{$orderId}}</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Waybill Upload</th>
                            <th>Sales Order No.</th>
                            <th>Invoice No.</th>
                            <th>Waybill</th>
                            @if($auth != 5)
                            <th>Approve</th>
                            @endif
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($tripwaybill))
                        <?php $counter = 0; ?>
                            @foreach($tripwaybill as $waybill)
                                <?php 
                                    $counter++;
                                    $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                                    if($waybill->waybill_status == 0) {
                                        $icon_class = 'icon-x text-danger-600';
                                        $icon = '<i class ="icon-x text-danger-600"></i>';
                                        $approve_status = '<i class ="icon-x text-danger-600"></i>';
                                    }
                                    else{
                                        $icon = '<a href="/assets/img/waybills/'.$waybill->photo.'" target="_blank"><i class="icon-eye8" title="Preview this Waybill"></i></a>';

                                        if($waybill->waybill_status == 1 && $waybill->approve_waybill == 0) {     $icon_class = 'icon-stamp text-info-600';
                                            $approve_status = "<input type='checkbox' name='approve' id='approveWaybill' value='$waybill->id'  >";

                                        } else {
                                            $icon_class = 'icon-stamp text-primary-600';
                                            $approve_status = "<input type='checkbox' checked disabled>";
                                        }
                                    }
                                ?>
                                <tr class="{{$css}}" style="font-size:10px">
                                    <td>{{$counter}}</td>
                                    <td class="text-center"><i class="{{$icon_class}}"></i></td>
                                    <td class="text-center">{!! $waybill->sales_order_no !!}</td>
                                    <td class="text-center">{!! $waybill->invoice_no !!}</td>
                                    <td class="text-center">{!! $icon !!}</td>
                                    @if($auth != 5)
                                    <td class="text-center">
                                        {!! $approve_status !!}
                                    </td>
                                    @endif
                                    <td>
                                        <div class="list-icons">
                                            <a href="{{URL('way-bill/'.$orderId.'/'.str_slug($client_name).'/'.$waybill->id)}}" class="list-icons-item text-primary-600">
                                                <i class="icon-pencil7"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="table-success" colspan="8">You have not uploaded any information as regards this waybill</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="table-info" colspan="7">
                            <span class="font-weight-semibold">Waybill:</span> 
                                @if(count($waybillstatus))
                                <label class="text-primary">{{$waybillstatus[0]['comment']}}<label>
                                @else
                                <label class="text-secondary">No update has been given about this</label>
                                @endif
                            </th>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /contextual classes -->


    </div>
</div>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/jquery.form.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/validatefile.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/waybill.js')}}"></script>
@stop