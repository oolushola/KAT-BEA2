@extends('layout')

@section('title')Kaya::.Add Incentives @stop

@section('main')

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
                    <span class="breadcrumb-item active">Outstanding (2)</span>
                </div>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
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
                    <h5 class="card-title">Payment Voucher
                        <span class="font-weight-bold">
                            @if(isset($recid))
                            ID: #{{ strtoupper($recid->uniqueId) }}
                            @endif
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="frmPaymentVoucher" action="{{URL('payment-voucher-request')}}">
                        @csrf
                        @if(isset($recid))
                            {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                        @endif
                        
                        <p class="mb-3 font-weight-bold text-right pointer text-success" style="text-decoration:underline" id="addMoreExpensesCategory">Add More</p>

                        @if(isset($recid) && count($recidDesc) > 0)
                            <div class="row mb-3 mb-md-2" id="moreExpenses">
                                @foreach($recidDesc as $voucherDesc)
                                    <input type="hidden" name="voucherDescIds[]" value="{{ $voucherDesc->id }}">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="description[]" placeholder="Description" value="{{ $voucherDesc->description }}"> 
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="owner[]" placeholder="Owner" value="{{ $voucherDesc->owner }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="margin:0; padding:0">
                                            <input type="number" step="0.01" class="form-control" placeholder="Amount" name="amount[]" id="amount"  value="{{ $voucherDesc->amount }}" style="margin:0; border-radius:0">
                                        </div>
                                    </div>                            
                                @endforeach
                            </div>
                        @else
                        <div class="row mb-3 mb-md-2" id="moreExpenses">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="description[]" value="" placeholder="Description"> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="owner[]" placeholder="Owner">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="margin:0; padding:0">
                                    <input type="number" step="0.01" class="form-control" placeholder="Amount" name="amount[]" id="amount" value="" style="margin:0; border-radius:0">
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

        <div class="col-md-7">
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
                                    <tr>
                                        <td>{{ $count += 1 }}</td>
                                        <td>
                                            @foreach($voucherArray as $descriptions)
                                                @if($descriptions->payment_voucher_id == $voucher->id)
                                                    <span class="d-block p-0 font-weight-semibold">
                                                        -
                                                        {{ $descriptions->description}} 
                                                        ({{ $descriptions->owner}}): 
                                                        {{ number_format($descriptions->amount, 2) }} 
                                                    </span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($voucher->voucher_status == FALSE)
                                                OPEN
                                            @else
                                                CLOSE
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{URL('payment-voucher-request/'.$voucher->id.'/edit')}}">
                                                <i class="icon-pen"></i>
                                            </a>
                                            <i class="icon-trash"></i>
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



