@extends('layout')

@section('title')Kaya::.Request Refunds @stop

@section('main')

@include('finance.vouchers._beneficiary')

<div class="content-wrapper">
    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Payment</span> - Refunds</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            
        </div>
        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="#" class="breadcrumb-item">Refund Log</a>
                    <span class="breadcrumb-item active">Outstanding Payments</span>
                </div>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <div class="row">
        <div class="col-md-7">
            &nbsp;
            <!-- Basic layout-->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Payment Voucher
                        <span class="font-weight-bold">
                            @if(isset($recid))
                            ID: #{{ strtoupper($recid->uniqueId) }}
                            @endif
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="frmPaymentVoucher" enctype="multipart/form-data" action="{{URL('payment-voucher-request')}}">
                        @csrf
                        @if(isset($recid))
                            {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                        @endif
                        <div class="row">
                            <div class="col-md-6 text-danger font-weight-bold" style="text-decoration:underline; cursor: pointer">
                                <a href=".beneficiary" data-toggle="modal" >Add Beneficiary</a>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-3 font-weight-bold text-right pointer text-success" style="text-decoration:underline" id="addMoreExpensesCategory">Add More</p>
                            </div>
                        </div>

                        @if(isset($recid) && count($recidDesc) > 0)
                            <div class="row mb-4 mb-md-2" id="moreExpenses">
                                @foreach($recidDesc as $voucherDesc)
                                    <input type="hidden" name="voucherDescIds[]" value="{{ $voucherDesc->id }}">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select type="text" class="form-control" name="expenseType[]">
                                                <option value="">Choose an Expense Type</option>
                                                @foreach($expenseTypes as $expenseType)
                                                @if(isset($recid) && $voucherDesc->expense_type_id == $expenseType->id)
                                                <option value="{{$expenseType->id}}" selected>{{$expenseType->expense_type}}</option>
                                                @else
                                                <option value="{{$expenseType->id}}">{{$expenseType->expense_type}}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <textarea class="form-control" name="description[]" value="" placeholder="Description" rows="1" column="10">{{ $voucherDesc->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select name="owner[]" id="owner" class="form-control">
                                                <option value="0">Pay to?</option>
                                                @foreach($possibleOwners as $owner)
                                                @if(isset($recid) && $voucherDesc->owner == $owner->first_name.' '.$owner->last_name )
                                                <option value="{{$owner->first_name}} {{ $owner->last_name}}" selected>{{$owner->first_name}} {{ $owner->last_name}}</option>
                                                @else
                                                <option value="{{$owner->first_name}} {{ $owner->last_name}}">{{$owner->first_name}} {{ $owner->last_name}}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin:0; padding:0">
                                            <input type="number" step="0.01" class="form-control" placeholder="Amount" name="amount[]" id="amount" value="{{ $voucherDesc->amount }}" style="margin:0; border-radius:0">
                                        </div>
                                    </div>                            
                                @endforeach
                            </div>
                        @else
                        <div class="row mb-3 mb-md-2" id="moreExpenses">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select type="text" class="form-control" name="expenseType[]">
                                        <option value="">Choose an Expense Type</option>
                                        @foreach($expenseTypes as $expenseType)
                                        <option value="{{$expenseType->id}}">{{$expenseType->expense_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <textarea class="form-control" name="description[]" value="" placeholder="Description" rows="1" column="10"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="owner[]" id="owner" class="form-control">
                                        <option value="0">Pay to?</option>
                                        @foreach($possibleOwners as $owner)
                                        <option value="{{$owner->first_name}} {{ $owner->last_name}}">{{$owner->first_name}} {{ $owner->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin:0; padding:0">
                                    <input type="number" step="0.01" class="form-control" placeholder="Amount" name="amount[]" id="amount" value="" style="margin:0; border-radius:0">
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="form-group" style="margin:0; padding:0">
                                    <input type="file" class="mt-2" name="attachment[]" value="" style="font-size:10px">
                                </div>
                            </div>                            
                        </div>
                        @endif

                        <div id="responsePlace"></div>

                        <div class="text-right" id="defaultButton">
                            @if(isset($recid))
                            <button type="button" class="btn btn-danger" id="updatePaymentVoucher">Update Expenses
                                <i class="icon-coins ml-2"></i>
                            </button>
                            @else
                            <button type="button" class="btn btn-danger" id="addPaymentVoucher">Push for Refund
                                <i class="icon-coins ml-2"></i>
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <!-- /basic layout -->

        </div>

        <div class="col-md-5">
        &nbsp;

            <!-- Contextual classes -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Preview Pane</h5>
                </div>

                <div class="table-responsive" id="contentHolder">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr style="font-size:11px;">
                                <th>#</th>
                                <th>Description</th>
                                <td>Status</td>
                                <th>Action</th>                         
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($paymentVoucher) > 0)
                                <?php $count = 0; ?>
                                @foreach($paymentVoucher as $voucher)
                                    <?php 
                                         if(
                                             $voucher->voucher_status == FALSE && 
                                             $voucher->check_status == FALSE && 
                                             $voucher->approved_status == FALSE) {
                                            $status = '<i class="icon-spinner3 spinner text-danger" title="Awaiting Verification"></i>';
                                            $editAndDeleteStatus = '';
                                         }
                                         elseif(
                                             $voucher->voucher_status == FALSE && 
                                             $voucher->check_status == TRUE && 
                                             $voucher->approved_status == FALSE) {
                                            $status = '<i class="icon-stamp text-primary" title="Verified"></i>';
                                            $editAndDeleteStatus = 'd-none';
                                         }
                                         elseif(
                                             $voucher->voucher_status == FALSE && 
                                             $voucher->check_status == TRUE && 
                                             $voucher->approved_status == TRUE) {
                                            $status = '<i class="icon-checkmark4 text-primary" title="Approved"></i>';
                                            $editAndDeleteStatus = 'd-none';
                                         }
                                         elseif(
                                             $voucher->voucher_status == TRUE && 
                                             $voucher->check_status == TRUE && 
                                             $voucher->approved_status == TRUE && 
                                             $voucher->upload_status == TRUE) {
                                            $status = '<i class="icon-checkmark4 text-success" title="Approved"></i>';
                                            $editAndDeleteStatus = 'd-none';
                                         }
                                         else{
                                            $status = '<i class="icon-stamp text-primary"></i>';
                                         }

                                         if($voucher->decline_status == TRUE) {
                                             $bgColor = 'bg-warning-300';
                                             $editStatus = "d-none";
                                             $flagStatus = '<i class="icon-flag8" title="This request has been flagged"></i>';
                                         }
                                         else {
                                             $bgColor = '';
                                             $editStatus = '';
                                             $flagStatus = '';
                                         }
                                         
                                    ?>
                                    <tr class="{{$bgColor}}" id="closeVoucher{{$voucher->id}}">
                                        <td>{{ $count += 1 }}</td>
                                        <td>
                                            @foreach($voucherArray as $descriptions)
                                                @if($descriptions->payment_voucher_id == $voucher->id)
                                                    <span class="d-block p-0 font-weight-semibold">
                                                        {{ $descriptions->expense_type }}:
                                                        {{ $descriptions->description}} 
                                                        ({{ $descriptions->owner}}): 
                                                        {{ number_format($descriptions->amount, 2) }} 
                                                    </span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($voucher->decline_status == TRUE)
                                            {!! $flagStatus !!}
                                            @else
                                            {!! $status !!} 
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{URL('payment-voucher-request/'.$voucher->id.'/edit')}}" class="{{$editAndDeleteStatus}}">
                                                @if($voucher->decline_status == FALSE)
                                                <i class="icon-pen"></i>
                                                @endif
                                            </a>
                                            <i id="{{$voucher->id}}" class="trashVoucher icon-trash {{ $editAndDeleteStatus }} pointer"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">You currently do not have an Open Refund Ticket</td>
                                </tr>
                            @endif
                        </tbody>
                    
                    </table>
                </div>
            </div>
            <!-- /contextual classes -->
        </div>
    </div>

</div>



@stop



