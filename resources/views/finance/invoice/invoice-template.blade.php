@extends('layout')

@section('title')Kaya ::. Invoicing @stop

@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/css/print-master.css')}}">
@stop

@section('main')
<div class="page-content">
<form method="POST" name="frmCompleteInvoice" id="frmCompleteInvoice">
	<input type="hidden" name="date_invoiced" value="{{date('d-m-Y')}}" />
	<input type="hidden" name="invoice_no_counter" value="{{$invoiceNumberCounter}}" />
	@csrf

	<!-- Main content -->
	<div class="content-wrapper">

		<!-- Page header -->
		<div class="page-header page-header-light">
			<div class="page-header-content header-elements-md-inline">
				<div class="page-title d-flex">
					<h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Invoices</span> - Templates</h4>
					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
				</div>

				<div class="header-elements d-none">
					<div class="d-flex justify-content-center">
						<a href="#" class="btn btn-link btn-float text-default"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a>
						<a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a>
						<a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a>
					</div>
				</div>
			</div>

			<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
				<div class="d-flex">
					<div class="breadcrumb">
						<a href="" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
						<a href="" class="breadcrumb-item">Invoices</a>
						<span class="breadcrumb-item active">Templates</span>
					</div>

					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
				</div>
				

				
			</div>
		</div>
		<!-- /page header -->

		<!-- Content area -->
		<div class="content">
		<?php
			if($vatRateInfos) {
				$withholdingTaxValue = $vatRateInfos->withholding_tax;
				$vatRateValue = $vatRateInfos->vat_rate;
			}
			else {
				$withholdingTaxValue = 5;
				$vatRateValue = 7.5;
			}
		?>

			<!-- Invoice template -->
			<div id="printableArea">
				<div class="card">
					<div class="card-header bg-transparent header-elements-inline">
						<h6 class="card-title">{!! date('d-m-Y') !!}</h6>
						<strong class="text-danger">Hey {{ ucwords(Auth::user()->first_name) }}, you are invoicing this client with: VAT: {{ $vatRateValue }}% & Withholding Tax: {{ $withholdingTaxValue }}%</strong>
						<div>
							<button type="button" class="btn btn-primary font-size-sm font-weight-semibold" data-toggle="modal" href="#salesOrderAndInvoiceNumber">CHANGE INVOICE NO, S.O NUMBER</button>
						</div>

					</div>
					

					<div class="card-body" style="font-family:tahoma; font-size:10px;">
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
									<h2 class="text-primary mb-2 mt-md-2">Invoice No: {!! $invoice_no !!}</h2>
										<ul class="list list-unstyled mb-0">
											
											<li>Date:<span class="font-weight-semibold">
												{!! date('d-m-Y') !!}
											</span></li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="mb-4 mb-md-2 col-md-6">
								<span class="text-muted">Billing To:</span>
								<ul class="list list-unstyled mb-0">
									<li>
										<h5 class="my-2">
											{!! strtoupper($clientInformation[0]->company_name) !!}
										</h5>
									</li>
									<li>{!! ucwords($clientInformation[0]->address) !!}</li>
									<li>Nigeria</li>
									<li>{!! $clientInformation[0]->phone_no !!}</li>
									<li><a href="#">{!! $clientInformation[0]->email !!}</a></li>
								</ul>
							</div>

							<div class="mb-2 col-md-6">
								<span class="text-muted">Payment Details:</span>
								<div class="d-flex flex-wrap wmin-md-400">
									<ul class="list list-unstyled mb-0">
										<li><h5 class="my-2">Total Due:</h5></li>
										<li>Bank name:</li>
										<li>Account Name:</li>
										<li>Account Number:</li>
										<li>Country:</li>
										<li>Tax Identification No:</li>
									</ul>

									<ul class="list list-unstyled text-right mb-0 ml-auto">
										<li><h5 class="font-weight-semibold my-2">
											&#x20a6;<span id="upperTotalDue"></span>
											</h5>
										</li>
										<li>{!! $companyProfile[0]->bank_name !!}</li>
										<li>{!! $companyProfile[0]->account_name !!}</li>
										<li>{!! $companyProfile[0]->account_no !!}</li>
										<li>Nigeria</li>
										<li>{!! $companyProfile[0]->tin !!}</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr style="font-size:10px; font-family:tahoma; font-weight:bold">
									<th><b>Invoice Date</b></th>
									<th><b>Customer</b></th>
									<th><b>Product</b></th>
									<th><b>Truck No.</b></th>
									
									<th><b>@if(isset($invoiceHeadings)){{$invoiceHeadings->sales_order_no_header}}@else{{'S.O. No.'}}@endif</b></th>
									<th><b>@if(isset($invoiceHeadings)){{$invoiceHeadings->invoice_no_header}}@else{{'Waybill No.'}}@endif</b></th>

									<th width="10%"><b>Ton</b></th>
									<th><b>Rate</b> </th>
								</tr>
							</thead>
							<tbody style="font-size:10px; font-family:tahoma">
								@if(count($invoicelists))
									<?php
										$subtotal = 0;
										$sumtotalIncentive = 0;
										$totalVatRate = 0;
										$incentiveVatRate = 0;
									?>
									@foreach($invoicelists as $key => $invoiceParams)
										<?php
											$trip_id = intval(str_replace('KAID', '', $invoiceParams->trip_id));
										?>

										<?php 
											$subtotal+=$invoiceParams->client_rate;
											$vat = $vatRateValue / 100 *  $invoiceParams->client_rate;
											$totalVatRate+=$vat;
										?>
										<tr>
											<td>{{date('d/m/Y',strtotime($invoiceParams->gated_out))}}</td>
											<td>
											<h6 class="mb-0">
												<a href="#">{!! $invoiceParams->customers_name  !!}</a>
												<span class="d-block font-size-sm text-muted">Destination: 
													{!! $invoiceParams->state !!}, {!! $invoiceParams->exact_location_id !!}
												</span>

												@foreach($availableIncentives as $locationBasedIncentive)
													@if($invoiceParams->exact_location_id === $locationBasedIncentive->exact_location)
														<input type="checkbox" name="addedIncentives[]" value="{{$locationBasedIncentive->id}}" >
														<input type="hidden" name="tripIdentity[]" value="{!! $invoiceParams->id !!}">
														<span class="font-size-sm">Incentive of <strong>&#x20a6;{{number_format($locationBasedIncentive->amount,2)}}</strong> is available for this location</span>
													@endif
												@endforeach


											</h6>
											<input type="hidden" name="trip_id[]" value="{!! $invoiceParams->id !!}">
										</td>
										<td>{!! $invoiceParams->product !!} </td>
										<td>{!! $invoiceParams->truck_no !!}</td>
										<td>
											@foreach($waybillinfos as $waybilldetails)
												@if($waybilldetails->trip_id == $invoiceParams->id)
													{!! $waybilldetails->sales_order_no !!}<br>
												@endif
											@endforeach
										</td>
										<td>
											@foreach($waybillinfos as $waybilldetails)
												@if($waybilldetails->trip_id == $invoiceParams->id)
													{!! $waybilldetails->invoice_no !!}<br>
												@endif
											@endforeach
										</td>
										<td>{!! $invoiceParams->tonnage /1000 !!}<br>
											@foreach($incentive as $incentivedesc)
												@if($incentivedesc->trip_id == $trip_id)
													<span style="font-size:10px; text-decoration:title">{{$incentivedesc->incentive_description}}</span>
												@endif
											@endforeach
										</td>
										<td>
											<span  class="initialRatePlaceholder">
												&#x20a6;{!! number_format($invoiceParams->client_rate, 2) !!}<br>
												@foreach($incentive as $incentivedesc)
													@if($incentivedesc->trip_id == $trip_id)
													&#x20a6;{{number_format($incentivedesc->amount, 2)}}
													@endif
												@endforeach
											</span>

											<span class="editClientRatePlaceholder" style="display:none">
												<input type="text" value="{{$invoiceParams->client_rate}}" name="initialAmount[]" style="outline:none;">
												<input type="hidden" value="{{$invoiceParams->trip_id}}" name="tripIdListings[]">
												<input type="hidden" value="{{$invoice_no}}" id="invoiceNumber">
											</span>
										</td>
										</tr>
											@foreach($incentive as $totalIncentive)
												@if($totalIncentive->trip_id == $trip_id)
												<?php $sumtotalIncentive += $totalIncentive->amount;
												$incentiveVatRate =  $vatRateValue /100 * $sumtotalIncentive; 
												
												?>
												
												@endif
											@endforeach
										

									@endforeach
								@else
									<tr>
										<td colspan="10">You didnt select any trip for invoicing</td>
									</tr>
								@endif
								
							</tbody>
						</table>
					</div>

					<div class="card-body" style="font-family:tahoma; font-size:11px; ">
						<div class="row d-md-flex flex-md-wrap">
							<div class="col-md-5 pt-2 mb-3">
								<h6 class="mb-3">Authorized Signatory</h6>
								<div class="mb-3">
									<img src="{{URL::asset('/assets/img/kaya/'.$companyProfile[0]->signatory)}}" width="100" alt="{{$companyProfile[0]->first_name}}">
								</div>

								<ul class="list-unstyled text-muted">
									<li>{!! ucfirst($companyProfile[0]->first_name) !!} {!! ucfirst($companyProfile[0]->last_name) !!}</li>
									<li>{!! $companyProfile[0]->phone_no !!}</li>
									<li>{!! $companyProfile[0]->email !!}</li>
									<li><em>For</em>: Kaya Africa Technologies Nig. Ltd</li>
								</ul>
							</div>
							

							<div class=" col-md-7 pt-2 mb-3 wmin-md-400 ml-auto">
								<div class="table-responsive">
									<table class="table">
										<tbody>
											<tr>
													<th><b>Subtotal (Actual):</b></th>
													<td class="text-right">&#x20a6;{!! number_format($subtotal, 2) !!}</td>
												</tr>
												<tr>
													<th><b>VAT:</b> <span class="font-weight-normal">({{$vatRateValue}}%)</span></th>
													<td class="text-right">&#x20a6;{!! number_format($totalVatRate, 2) !!}</td>
												</tr>
												<tr>
													<th><b>Subtotal (Incentive):</b></th>
													<td class="text-right">&#x20a6;{!! number_format($sumtotalIncentive, 2) !!}</td>
												</tr>
												<tr>
													<th><b>VAT:</b> <span class="font-weight-normal">({{$vatRateValue}}%)</span></th>
													<td class="text-right">&#x20a6;{!! number_format($incentiveVatRate, 2) !!}</td>
												</tr>
												<tr>
													<th><b>TOTAL:</b></th>
													<td class="text-right text-primary"><h5 class="font-weight-semibold">&#x20a6;{!! number_format($subtotal + $totalVatRate + $sumtotalIncentive + $incentiveVatRate, 2) !!}</h5></td>
													<input id="sumtotalIncentive" type="hidden" value="{!! number_format($subtotal + $totalVatRate + $sumtotalIncentive + $incentiveVatRate, 2) !!}">

												</tr>
											</tbody>
									</table>
								</div> 

								<input type="text" name="vat_used" value="{{ $vatRateValue }}">
								<input type="text" name="withholding_vat_used" value="{{ $withholdingTaxValue }}">

								<div class="text-right mt-3">
									<span id="loader"></span>

									<button type="button" id="editInvoice" class="btn btn-light btn-sm ml-3"><i class="icon-pencil mr-2"></i> Edit Rate</button>

									<button type="button" class="btn btn-danger btn-sm ml-3" id="exitUpdateBtn" style="display:none"><i class="icon-x mr-2"></i>Cancel</button>


									<button type="submit" id="completeInvoice" class="btn btn-primary btn-labeled btn-labeled-left"><b><i class="icon-paperplane"></i></b>Done</button>

									<div class="hidden" id="saveAndPrintContainer">
										<button type="button" class="btn btn-light btn-sm"><i class="icon-file-check mr-2"></i> Save</button>
										<button type="button" id="printInvoice" class="btn btn-light btn-sm ml-3"><i class="icon-printer mr-2"></i> Print</button>
									</div>

								</div>
							</div>
						</div>
					</div>

					

					<div class="card-footer">
						<span class="text-muted">Thank you for choosing Kaya Africa Technology Nigeria Limited as your trusted logistic partner. This invoice can be paid via Bank transfer, or any other payment means convenient.</span>
					</div>
				</div>
			</div>
			<!-- /invoice template -->



		</div>
		<!-- /content area -->


	</div>
	<!-- /main content -->
</form>

	
	
@include('finance.invoice.partials._invoice-and-sales-update')	
	
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

	$totalAmount = $("#sumtotalIncentive").val();
	$('#upperTotalDue').html($totalAmount);


	$('#editInvoice').click(function(){
			$('#updateInvoice').css({display:'inline-block'});
			$('#exitUpdateBtn').css({display:'inline-block'});
			$('.editClientRatePlaceholder').css({display:'inline-table'});
			$('.initialRatePlaceholder').css({display:'none'});
			$('#printInvoice').css({display:'none'});
			$('#saveInvoice').css({display:'none'});
			$(this).css({display:'none'});
		});

		$('#exitUpdateBtn').click(function(){
			$('#updateInvoice').css({display:'none'});
			$('.editClientRatePlaceholder').css({display:'none'});
			$('.initialRatePlaceholder').css({display:'inline-table'});
			$('#printInvoice').css({display:'inline-block'});
			$('#saveInvoice').css({display:'inline-block'});
			$('#editInvoice').css({display:'inline-block'});
			$(this).css({display:'none'});			
		});

		$('#updateInvoice').click(function(){
			$invoiceNumber = $('#invoiceNumber').val();
			$('#loader').html('<i class="icon-spinner spinner"></i> Please wait...');
			$.post('/update-initial-invoice-price', $('#frmUpdateInitialPrice').serializeArray(), function(response){
				if(response == 'amountUpdated'){
					$url = `/invoice-trip/${$invoiceNumber}`;
					window.location.href=$url;
				} else {

				}
			})
		})






</script>
@stop