@extends('layout')

@section('title')Kaya ::. Local Purchase Order for Trip {{$trip_id}} @stop

@section('css')

<link rel="stylesheet" type="text/css" href="{{URL::asset('/css/print-master.css')}}">
@stop

@section('main')
<div class="page-content">


	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header page-header-light">
			<div class="page-header-content header-elements-md-inline">
				<div class="page-title d-flex">
					<h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Local Purchase Order</span> - Templates</h4>
					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
				</div>

				<div class="header-elements d-none">
					<div class="d-flex justify-content-center">
						<a href="#" class="btn btn-link btn-float text-default"><i class="icon-bars-alt text-primary"></i><span>Periscope</span></a>
						<a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Orders</span></a>
					</div>
				</div>
			</div>

			<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
				<div class="d-flex">
					<div class="breadcrumb">
						<a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
						<a href="{{URL('local-purchase-order')}}" class="breadcrumb-item">Local Purchase Order</a>
						<span class="breadcrumb-item active">LPO</span>
					</div>

					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
                </div>
                
                <div class="header-elements d-none">
                <div class="breadcrumb justify-content-center">
                    <a href="#" class="breadcrumb-elements-item">
                        <i class="icon-feed mr-2"></i>
                        LPO Archive
                    </a>

                </div>
            </div>

				
			</div>
		</div>
		<!-- /page header -->


		<!-- Content area -->
		<div class="content">

			<!-- Invoice template -->
			<div id="printableArea">
				<div class="card">
					<div class="card-header bg-transparent header-elements-inline">
						<h6 class="card-title">{!! date('d-m-Y') !!}</h6>
					</div>

					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-4">
									<img src="{{URL::asset('/assets/img/kaya/'.$companyProfile[0]->company_logo)}}" class="mb-3 mt-2" alt="company's Logo" style="width: 100px;">
									<ul class="list list-unstyled mb-0">
										<li>{!! $companyProfile[0]->address !!}</li>
										<li>Lagos, Nigeria</li>
										<li><a href="#">{!! $companyProfile[0]->website !!}</a> | 
											<a href="#">{!! $companyProfile[0]->company_email !!}</a>
										</li>
										<li>{!! $companyProfile[0]->company_phone_no !!}</li>
									</ul>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-4" style="float:right">
									<div class="text-sm-right">
										<h2 class="text-primary mb-2 mt-md-2">
                                            <?php 
                                            $counter = intval('0000') + $lposummary[0]->id;
                                            $lpoNumber = sprintf('%04d', $counter);
                                            $lpo = 'KLPO-'.$lpoNumber; ?>
                                            {{$lpo}}
                                        </h2>
										<ul class="list list-unstyled mb-0">
											<li>Current Date:<span class="font-weight-semibold">
												{!! date('d-m-Y') !!}
											</span></li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="mb-4 mb-md-2 col-md-6">
								<span class="text-muted">Payment To:</span>
								<ul class="list list-unstyled mb-0">
									<li>
										<h5 class="my-2">
											{!! strtoupper($transporterInformation[0]->transporter_name) !!}
										</h5>
									</li>
									<li>{!! $transporterInformation[0]->phone_no !!}</li>
								</ul>
							</div>

							<div class="mb-2 col-md-6">
								<span class="text-muted">Payment Details:</span>
								<div class="d-flex flex-wrap wmin-md-400">
									<ul class="list list-unstyled mb-0">
										<li><h5 class="my-2">Total Payable:</h5></li>
										<li>Bank name:</li>
										<li>Account Name:</li>
										<li>Account Number:</li>
										<li>Country:</li>
									</ul>

									<ul class="list list-unstyled text-right mb-0 ml-auto">
										<li><h5 class="font-weight-semibold my-2">
											&#x20a6;{!! number_format($lposummary[0]->transporter_rate,2) !!}
											</h5>
										</li>
										<li><span class="font-weight-semibold">{!! $transporterInformation[0]->bank_name !!}</span></li>
										<li>{!! $transporterInformation[0]->account_name !!}</li>
										<li>{!! $transporterInformation[0]->account_number !!}</li>
										<li>Nigeria</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Customer</th>
									<th>Product</th>
									<th>Truck No.</th>
									<th>S.O. No.</th>
									<th>Waybill No.</th>
									<th>Ton</th>
									<th>Rate</th>
								</tr>
							</thead>
							<tbody>
								@if(count($lposummary))
									<?php
										$subtotal = 0;
										$totalVatRate = 0;
									?>
									@foreach($lposummary as $lpoParams)
										<?php 
											$date = date('Y-m-d', strtotime($lpoParams->gated_out));
											if($date > date('2021-02-15')) {
												$vatValue = 7.5;
												$vat = $vatValue / 105 *  $lpoParams->transporter_rate;
											}
											else {
												$vatValue = 5;
												$vat = $vatValue / 107 *  $lpoParams->transporter_rate;
											}                      
                      $subtotal+=$lpoParams->transporter_rate - $vat;
											$totalVatRate+=$vat;
										?>
										<tr>
											<td>
											<h6 class="mb-0">
												<a href="#">{!! $lpoParams->customers_name  !!}</a>
												<span class="d-block font-size-sm text-muted">Destination: 
													 {!! $lpoParams->exact_location_id !!}
												</span>
											</h6>
										</td>
										<td>{!! $lpoParams->product !!} </td>
										<td>{!! $lpoParams->truck_no !!}</td>
										<td>
											@foreach($waybillinfos as $waybilldetails)
												@if($waybilldetails->trip_id == $lpoParams->id)
													{{ $waybilldetails->sales_order_no }}<br />  
												@endif
											@endforeach
										</td>
										<td>
											@foreach($waybillinfos as $waybilldetails)
												@if($waybilldetails->trip_id == $lpoParams->id)
													{!! $waybilldetails->invoice_no !!}<br>
												@endif
											@endforeach
										</td>
										<td>{!! $lpoParams->tonnage /1000 !!}</td>
										<td>&#x20a6;{!! number_format($subtotal, 2) !!}</td>

										</tr>
									@endforeach
								@else
									<tr>
										<td colspan="10">You didnt select any trip for invoicing</td>
									</tr>
								@endif
								
							</tbody>
						</table>
					</div>

					<div class="card-body">
						<div class="row">
							<div class="pt-2 mb-3 col-md-5">
								<h5 class="font-weight-bold">Payment Details.</h5>
								
								@if(count($payments))
									<?php $counter = 0;  ?>
									@foreach($payments as $payment)
									<?php $counter += 1;  ?>
										<p>{{ $counter }}. {{ $payment->payment_for }}: &#x20a6;{{ number_format($payment->amount, 2) }} <span class="badge badge-danger">{{ $payment->paid_time_stamps }}</span></p>
									@endforeach
								@else
										<p>No payment log can be found for this trip.</p>
								@endif
							</div>

							<div class="pt-2 mb-3 wmin-md-400 ml-auto col-md-7">
								<div class="table-responsive">
									<table class="table">
										<tbody>
											<tr>
												<th>Subtotal:</th>
												<td class="text-right">&#x20a6;{!! number_format($subtotal, 2) !!}</td>
											</tr>
											<tr>
												<th>VAT: <span class="font-weight-normal">({{$vatValue}}%)</span></th>
												<td class="text-right">&#x20a6;{!! number_format($totalVatRate, 2) !!}</td>
											</tr>
											<tr>
												<th>Total:</th>
												<td class="text-right text-primary"><h5 class="font-weight-semibold">&#x20a6;{!! number_format($subtotal + $totalVatRate, 2) !!}</h5></td>
											</tr>
										</tbody>
									</table>
								</div>

								<div class="text-right mt-3">

									<div id="saveAndPrintContainer">
										<button type="button" class="btn btn-light btn-sm"><i class="icon-file-check mr-2"></i> Save</button>
										<button type="button" id="printInvoice" class="btn btn-light btn-sm ml-3"><i class="icon-printer mr-2"></i> Print</button>
									</div>

								</div>
							</div>
						</div>
					</div>

					

					<div class="card-footer">
						<span class="text-muted">Thank you for collaborating with Kaya Africa Technology Nigeria Limited in redefining the face of logistics in Nigeria. Together we can acheive more.</span>
					</div>
				</div>
			</div>
			<!-- /invoice template -->



		</div>
		<!-- /content area -->

		


	</div>
	<!-- /main content -->


	
	
	
	
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/invoice.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/printThis.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/print.js')}}"></script>
<script>
	$("#printInvoice").click(function() {
		$("#saveAndPrintContainer").addClass("hidden");
		$.print("#printableArea");
		window.setTimeout(() => {
			$("#saveAndPrintContainer").addClass("show").removeClass('hidden');			
		}, 3000);
	})
</script>
@stop