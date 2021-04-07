@extends('layout')

@section('title')Kaya ::. Moving Africa @stop

@section('css')
<style>
input{
	outline:none
}
</style>
@stop

@section('main')
<div class="page-content">
	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header page-header-light">
			<div class="page-header-content header-elements-md-inline">
				<div class="page-title d-flex">
					<h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Payment</span> - Vouchers</h4>
					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
				</div>
			</div>

			<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
				<div class="d-flex">
					<div class="breadcrumb">
						<a href="{{URL('/')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
						<a href="#" class="breadcrumb-item">Voucher</a>
						<span class="breadcrumb-item active"></span>
					</div>

					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
				</div>

				
			</div>
		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">
			<!-- Invoice template -->
			<div id="printableArea">
				<div class="card" style="border:none">
					<div class="card-body" style="font-family:tahoma; font-size:12px;">
						<div class="row">
							<div class="col-md-6">
								<div class="">
									<img src="{{URL::asset('/assets/img/kaya/'.$companyProfile[0]->company_logo)}}" class="mb-3 mt-2" alt="company's Logo" style="width: 70px;">
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-4" style="float:right">
									<div class="text-sm-right">
										<span id="defaultInvoiceNoAndDate">
											<h2 class="text-primary mb-2 mt-md-2">{{ strtoupper($voucher->uniqueId) }}</h2>
											
										</span>
									</div> 
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<span class="font-weight-bold"></span>
								<ul class="list list-unstyled mb-0">
									<div id="defaultCompanyInfo" style="font-size:13px">
										<li class="font-weight-bold">3A Gbenga Ademulegun, Lane</li>
                                        <li class="font-weight-bold">Parkview Estate</li>
                                        <li class="font-weight-bold">Ikoyi, Lagos</li>
										<li class="font-weight-bold">Nigeria</li>
									</div>
								</ul>
							</div>

							<div class="mb-2  col-md-6">
								<span class="text-muted">Payment Details:</span>
								<div class="d-flex flex-wrap wmin-md-400">
									<ul class="list list-unstyled mb-0">
										<li><h6 class="my-1 font-weight-bold">Total Due:</h6></li>
										<li>Staff Name:</li>
                                        
										
									</ul>

									<ul class="list list-unstyled text-right mb-0 ml-auto">
										<li><h6 class="font-weight-bold my-1">&#x20a6;{{ number_format($sum, 2) }}</h6>
										</li>
										<li><h6 class="font-weight-bold my-1"></h6>
										</li>
										
									</ul>
								</div>
							</div>
						</div>
					</div>

                    <h3 class="text-center mt-4 mb-4 font-weight-bold">PAYMENT VOUCHER</h3>

					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr style="font-size:13px; font-family:tahoma; font-weight:bold" >
									<th><b>SN</b></th>
									<th width="70%"><b>DESCRIPTION</b></th>
									<th><b>OWNED BY</b></th>
									<th><b>AMOUNT</b></th>
								</tr>
							</thead>
							<tbody style="font-size:13px; font-family:tahoma">
                                <?php $count = 0; $sum = 0; ?>
                                @if(count($voucherDesc))
                                    @foreach($voucherDesc as $desc)
                                        <?php $sum += $desc->amount; ?>
                                        <tr>
                                            <td>{{ $count += 1 }}</td>
                                            <td class="font-weight-bold"><pre style="border:none; padding: 0; margin:0">{{ strtoupper($desc->description) }}</pre></td>
                                            <td>{{ $desc->owner }}</td>
                                            <td class="font-weight-bold">&#x20a6;{{ number_format($desc->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                            <td colspan="3" class="font-weight-bold text-right" style="border:none">TOTAL</td>
                                            <td class="font-weight-bold">&#x20a6;{{ number_format($sum, 2)}}</td>
                                        </tr>
                                @else

                                @endif
							</tbody>
						</table>
                        &nbsp;
					</div>

                    <section class="row m-2 mt-4 mb-3">
                        <div class="col-md-3"></div>
						<div class="col-md-6 text-center font-weight-bold">
                            <span class="d-block ml-2 mr-2 font-weight-bold mt-2" style="border-bottom:2px dotted #000">
                                {{ strtoupper(ucwords($amountInWords)) }}
                            </span>
                            Amount in Words
                        </div>
						<div class="col-md-3"></div>
                    </section>

                    <section class="row mt-4 m-2 mb-4">
                        <div class="col-md-4 text-center font-weight-bold">
                            <span class="d-block ml-2 mr-2 mt-5 font-weight-bold" style="border-bottom:2px dotted #000">
                                <small>{{ $voucher->request_timestamps}}</small><br>
                                {{ strtoupper($requester->first_name) }} {{ strtoupper($requester->last_name) }}
                            </span>
                            Requested By
                        </div>
                        <div class="col-md-4 text-center font-weight-bold">
                            <span class="d-block ml-2 mr-2 mt-5 font-weight-bold" style="border-bottom:2px dotted #000">
                                <small>{{ $voucher->checked_timestamps}}</small><br>
                                {{ strtoupper($checker->first_name) }} {{ strtoupper($checker->last_name) }}
                            </span>
                            Checked By
                        </div>
                        <div class="col-md-4 text-center font-weight-bold">
							<img src="{{URL::asset('assets/img/kaya/autha.png')}}" alt="authroizedapproval" srcset="" width="50" height="50">
                            <span class="d-block ml-2 mr-2 font-weight-bold mt-2" style="border-bottom:2px dotted #000">
                                <small>{{ $voucher->approval_timestamps}}</small><br>
                                {{ strtoupper($approval->first_name) }} {{ strtoupper($approval->last_name) }}
                            </span>
                            Approved by
                        </div>
                    </section>

					<div class="card-body" style="font-family:tahoma; font-size:13px; ">
						<div class="row">
							<div class="pt-2 mb-3 wmin-md-400 ml-auto col-md-8">
								<div class="text-right mt-3">
									<span id="loader"></span>

									<div class="hideOnPrint">
										<button type="button" class="btn btn-danger btn-sm ml-3" id="exitUpdateBtn" style="display:none"><i class="icon-x mr-2"></i>Cancel</button>
										<button type="button" id="printVoucher" class="btn btn-light btn-sm ml-3"><i class="icon-printer mr-2"></i> Print</button>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /content area -->


	</div>
	<!-- /main content -->
</form>
	
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/print.js')}}"></script>
  <script>
    $(function() {
        $("#printVoucher").click(function() {
			$(".hideOnPrint").addClass("hidden");
             $.print("#printableArea");
            window.setTimeout(() => {
				$(".hideOnPrint").addClass("show").removeClass('hidden');
            }, 3000);
		})
	});

	
	
</script>
@stop

