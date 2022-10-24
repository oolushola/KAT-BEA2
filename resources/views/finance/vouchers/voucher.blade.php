@extends('layout')

@section('title')Kaya::.Vouchers @stop

@section('main')

<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Payment Vouchers</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            
        </div>
        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="#" class="breadcrumb-item">Refund Log ({{ count($paymentVouchers)}})</a>
                    <span class="breadcrumb-item active pointer" id="pendingUploads">Pending Uploads ({{ count($unpaidVouchers) }})</span>
                </div>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->
    

    <input type="hidden" id="checkStatus" value="1"> 

    <div class="row">
        <div class="d-none" id="availableUploads">
            &nbsp;

            <!-- Basic layout-->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title font-size-sm font-weight-bold"> 
                        Upload All Vouchers for Payment <input type="checkbox" class="ml-2" id="checkAllVoucherUploads">
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" name="frmUploadPaymentVoucher" id="frmUploadPaymentVoucher" enctype="multipart/form-data">
                        @csrf {!! method_field('PATCH') !!}
                        <div class="row">
                            @if(count($unpaidVouchers))
                                @foreach($unpaidVouchers as $key => $voucher)
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="text-success font-weight-bold font-size-xs mb-3">
                                                    {{ strtoupper($voucher->uniqueId) }}
                                                    <span class="text-primary font-weight-bold font-size-xs" style="float:right;">
                                                        Requested by: {{ ucfirst($people[$key]->first_name)}}
                                                    </span>
                                                </p>
                                                    <?php $count = 0; $sumTotal = 0; ?>
                                                    @foreach($unpaidVouchersDesc as $desc)
                                                        @if($desc->payment_voucher_id == $voucher->id)
                                                        <?php $sumTotal += $desc->amount; ?>
                                                        <span class="d-block mt-1 font-weight-semibold" style="font-size:12px">
                                                            ({{ $count += 1 }}) {{ $desc->expense_type }}: {{$desc->description}} &#x20A6;{{ number_format($desc->amount, 2) }}
                                                            @if($desc->attachment)
                                                            <a target="_blank" href="{{URL::asset('assets/img/vouchers/'.$desc->attachment.'')}}"><i class="icon-attachment ml-4"></i></a>
                                                            @endif
                                                        </span>
                                                        @endif
                                                    @endforeach
                                                <h5 class="mt-2 font-weight-bold mb-0">Total: &#x20A6;{{ number_format($sumTotal, 2) }}
                                                    <input type="checkbox" name="voucherIds[]" value="{{$voucher->id}}" id="" class="ml-1 paymentVoucherUploads"  >
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-md-12">
                                    <span id="loader"></span>
                                    <button type="submit" class="btn btn-primary mt-2" id="uploadPaymentVouchers">Upload 
                                        <i class="icon-cloud-upload ml-2"></i>
                                    </button>
                                </div>
                            @else
                                <h5>You do not have any voucher to Upload.</h5>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <!-- /basic layout -->
        </div>
        <div class="col-md-12" id="defaultView">
            &nbsp;
            <!-- Contextual classes -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Log of Vouchers</h5>
                </div>

                <div class="ml-3 mb-2 mt-2 font-size-md">{!! $paymentVouchers->links() !!}</div>
                <div class="table-responsive" id="contentHolder">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr style="font-size:11px;">
                                <th>#</th>
                                <th>Date</th>
                                <th>Voucher ID</th>
                                <th>Requested By:</th>
                                <th>Amount</th>
                                <th>Action</th>                         
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($paymentVouchers))
                                <?php $count = 0; ?>
                                @foreach($paymentVouchers as $key=> $voucher)
                                    <tr class="font-size-xs">
                                        <td>{{ $count+= 1}}</td>
                                        <td>{{ $voucher->request_timestamps }}</td>
                                        <td class="font-weight-semibold">
                                            {{ strtoupper($voucher->uniqueId) }}
                                            @foreach($voucherDescriptions as $vdesc)
                                            @if($vdesc->payment_voucher_id == $voucher->id && $vdesc->attachment)
                                            <a target="_blank" href="{{URL::asset('assets/img/vouchers/'.$vdesc->attachment.'')}}">
                                                <i class="icon-attachment ml-1 mr-2"></i>
                                            </a>
                                            @endif
                                            @endforeach
                                            
                                        </td>
                                        <td class="font-weight-semibold">{{ ucfirst($users[$key]->first_name) }} {{ ucfirst($users[$key]->last_name) }}</td>
                                        <td>&#x20A6;{{ number_format($sumTotal[$key], 2) }}</td>
                                        <td><a href="{{URL('payment-voucher/'.$voucher->uniqueId.'/'.md5($voucher->id).'')}}">View</a> <i class="icon-cloud-upload"></i></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">There are no vouchers yet</td>
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



