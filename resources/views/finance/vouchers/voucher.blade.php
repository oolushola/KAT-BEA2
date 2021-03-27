@extends('layout')

@section('title')Kaya::.Add Incentives @stop

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
                    <a href="#" class="breadcrumb-item">Refund Log(1200)</a>
                    <span class="breadcrumb-item active">Pending Approval (2)</span>
                </div>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <div class="row">
        

        <div class="col-md-12">
        &nbsp;

            <!-- Contextual classes -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Log of Vouchers</h5>
                </div>

                <div class="table-responsive" id="contentHolder">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr style="font-size:11px;">
                                <th>#</th>
                                <th>Voucher ID</th>
                                <th>Requested By:</th>
                                <th>Status</th>
                                <th>Action</th>                         
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($paymentVoucher))
                                <?php $count = 0; ?>
                                @foreach($paymentVoucher as $key=> $voucher)
                                    <tr class="font-size-xs">
                                        <td>{{ $count+= 1}}</td>
                                        <td class="font-weight-semibold">
                                            {{ strtoupper($voucher->uniqueId) }}
                                        </td>
                                        <td class="font-weight-semibold"></td>
                                        <td>
                                        </td>
                                        <td>Print</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5">You currently do not have anything to view</td>
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



