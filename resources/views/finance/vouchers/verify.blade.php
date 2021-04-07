@extends('layout')

@section('title')Kaya ::. Drivers @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Payment Vouchers</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#assignAccountManager" data-toggle="modal" class="btn btn-link btn-float text-default font-weight-semibold"><i class="icon-truck"></i> <span>Assign Account Manager</span></a>
                
                <a href="{{URL('kaya-target')}}" class="btn btn-link btn-float text-default font-weight-semibold"><i class="icon-calendar2"></i> <span>Targets</span></a>
                
                <a href="{{URL('buh-target')}}" class="btn btn-link btn-float text-default font-weight-semibold"><i class="icon-pointer"></i> <span>Target Margin</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title font-size-sm font-weight-bold"> 
                    Verify All Payment Vouchers <input type="checkbox" class="ml-2" id="checkAllPaymentVouchers">
                </h5>
            </div>

            <div class="card-body">
                <form method="POST" name="frmPaymentVoucher" id="frmPaymentVoucher" enctype="multipart/form-data">
                    @csrf {!! method_field('PATCH') !!}
                    <div class="row">
                        @if(count($getUnverifiedVouchers))
                            @foreach($getUnverifiedVouchers as $key => $voucher)
                                <div class="col-md-3 col-sm-6 col-xs-12" style="max-height:400px; overflow:auto">
                                    <div class="card">
                                        <div class="card-body">
                                            <p class="text-success font-weight-bold font-size-xs mb-3 pointer paymentBreakdown" id="{{$voucher->uniqueId}}" value="0">
                                                {{ strtoupper($voucher->uniqueId) }}
                                                <span class="text-primary font-weight-bold font-size-xs" style="float:right;">
                                                    Requested by: {{ ucfirst($users[$key]->first_name)}}
                                                </span>
                                            </p>
                                                <?php $count = 0; $sumTotal = 0; ?>
                                                @foreach($voucherDescArray as $desc)
                                                    @if($desc->payment_voucher_id == $voucher->id)
                                                    <?php $sumTotal += $desc->amount; ?>
                                                    <span class="d-block mt-1 font-weight-semibold" style="font-size:12px">
                                                        <pre class="d-none voucher{{ $voucher->uniqueId }}" style=" font-size: 11px; font-family:tahoma">
                                                            @if($desc->attachment)
                                                                <a target="_blank" href="{{URL::asset('assets/img/vouchers/'.$desc->attachment.'')}}"><i class="icon-attachment ml-4"></i></a>
                                                            @endif
                                                            ({{ $count += 1 }}) {{$desc->description}} &#x20A6;{{ number_format($desc->amount, 2) }}
                                                        </pre>
                                                    </span>
                                                    @endif
                                                @endforeach
                                            <h5 class="mt-2 font-weight-bold mb-0">Total: &#x20A6;{{ number_format($sumTotal, 2) }}
                                                <input type="checkbox" name="voucherIds[]" value="{{$voucher->id}}" id="" class="ml-1 paymentVouchers"  >
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="text-right">
                                <span id="loader"></span>
                                <button type="submit" class="btn btn-primary mt-2" id="verifyPaymentVoucher">Verify 
                                    <i class="icon-stamp ml-2"></i>
                                </button>
                            </div>
                        @else
                            <h5>Yipee! You do not have any voucher to verify.</h5>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <!-- /basic layout -->
    </div>

    <div class="col-md-6"></div>

</div>
@stop

