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
            <p style="font-size:10px; color:red; font-weight:bold">NOTE: If this trip has just  an SO or INV other box should be filled with an hyphen "-"</p>
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Waybill</h5>
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
                    <input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id }}" >
                    <input type="hidden" name="tracker" id="tracker" value="{{$tracker}}" >
                    <input type="hidden" name="waybill_name" value="{{$orderId}}{{$client_name}}">
                @csrf
                                        
                    @if(!isset($recid))
                    <span class="error font-weight-semibold" id="addMore" style="cursor:pointer">Add More...</span>
                    @endif

                    <div class="row mb-md-2 input_field_wraps">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" class="form-control salesOrderNumber" placeholder="S.O.  Number" name="sales_order_no[]" value="@if(isset($recid)){{$recid->sales_order_no}}@endif">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" class="form-control invoiceNumber" placeholder="Invoice No" name="invoice_no[]" value="@if(isset($recid)){{$recid->invoice_no}}@endif" >
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <span style="font-size:10px; font-weight:bold; color:blue">Upload Waybill</span>
                                <input type="file" name="photo[]" style="font-size:9px;" class="waybillChooser">
                            </div>
                        </div>
                    </div>

                    
                    
                    
                    
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
                <h5 class="card-title">Status : {{$orderId}}</h5>
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

                                            <span href="#" class="list-icons-item text-danger-600">
                                                <i class="icon-trash deleteSpecificWaybill" value="{{$waybill->id}}" ></i>
                                            </span>

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
                            <span class="font-weight-semibold">Status:</span> 
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
            &nbsp;
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <form action="{{URL('upload-signed-waybill')}}" method="POST" id="frmUploadEir" enctype="multipart/form-data">
                            @csrf
                            <table class="table table-bordered">
                                <thead class="table-info">
                                    <tr>
                                        <th colspan="5">Waybill Collector</th>
                                        <input type="hidden" value="{{$tripId}}" name="waybillTripId" />
                                        <input type="hidden" value="" name="waybillType" id="waybillType" />
                                    </tr>
                                    <tr>
                                        <th>
                                            <span class="font-size-xs">EIR / Signed Waybill 
                                                <input type="radio" name="waybillCategory" class="waybillType" value="Waybill">
                                            </span>
                                            <span class="font-size-xs ml-2">Pod
                                                <input type="radio" name="waybillCategory" class="waybillType" value="POD">
                                            </span>
                                        </th>
                                        <th colspan="4">
                                            <input type="file" id="signedWaybill" name="signedWaybill" class="font-size-xs d-inline-block" style="width: 150px" />
                                            <input type="text" placeholder="Waybill No" id="waybillNo" name="waybill_no" class="d-inline-block" style="border:1px solid #ccc;  outline: none" />
                                            <button class="font-size-xs btn btn-primary" id="addSignedWaybill">ADD SIGNED WAYBILL</button>
                                            <span id="waiter"></span>
                                        </th>
                                    <tr>
                                    <tr style="font-size:11px;" class="text-center">
                                        <th>SN</th>
                                        <th>Preview Container No.</th>
                                        <th>Remark</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($offloadWaybillLists))
                                    <?php $counter = 0; ?>
                                        @foreach($offloadWaybillLists as $eir)
                                            <?php 
                                                $counter++;
                                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                                            ?> 
                                            <tr class="{{$css}} text-center" style="font-size:10px">
                                                <td>{{$counter}}</td>
                                                <td>
                                                    <a href="{{URL::asset('assets/img/signedwaybills/'.$eir->received_waybill.'')}}" target="_blank">
                                                        {{ $eir->container_card_no }}
                                                    </a>
                                                </td>
                                                <td>{{ $eir->waybill_remark }}</td>
                                                <td class="text-center">
                                                    <i class="icon-trash pointer unlinkSignedWaybill" id={{$eir->id}}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="table-success" colspan="8">You have not uploaded any information as regards this waybill</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                
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