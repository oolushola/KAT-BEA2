@extends('layout')

@section('title')Kaya ::. {{$invoice_no}} @stop

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
					<h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Invoices</span> - Templates</h4>
					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
				</div>
			</div>

			<div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
				<div class="d-flex">
					<div class="breadcrumb">
						<a href="{{URL('/')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
						<a href="{{URL('invoices')}}" class="breadcrumb-item">Invoices</a>
						<span class="breadcrumb-item active">{{$invoice_no}}</span>
					</div>

					<a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
				</div>

				
			</div>
		</div>
		<!-- /page header -->

		<!-- Content area -->
		<?php
			$withholdingTaxValue = $vatRateInfos->withholding_tax;
			$vatRateValue = $vatRateInfos->vat_rate;
		?>
		<div class="content">
			<form id="frmReprintInvoice" method="POST">
				@csrf
			<!-- Invoice template -->
			<div id="printableArea">
				<div class="card">
					<div class="card-header bg-transparent header-elements-inline">
                        <h6 class="card-title"><?php 
                            $array = explode(" ", $dateInvoiced[0]->created_at);
                            //echo date('d-m-Y', strtotime($array[0]));
                            
						?></h6>
						
						<div class="deleteInvAndTonsplitter">
							<button type="button" class="btn btn-danger deleteInvoice font-size-sm font-weight-semibold" value="{{$invoice_no}}">DELETE INVOICE</button>&nbsp;
							<button type="button" class="btn btn-primary font-size-sm font-weight-semibold" data-toggle="modal" href="#salesOrderAndInvoiceNumber">CHANGE INVOICE NO, S.O NUMBER</button>
						</div>
					</div>

					<div class="card-body" style="font-family:tahoma; font-size:12px;">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-4">
									<img src="{{URL::asset('/assets/img/kaya/'.$companyProfile[0]->company_logo)}}" class="mb-3 mt-2" alt="company's Logo" style="width: 100px;">
									<ul class="list list-unstyled mb-0">
										<li class="font-weight-bold">{!! $companyProfile[0]->address !!}</li>
										<li class="font-weight-bold">Lagos, Nigeria</li>
										<li class="font-weight-bold"><a href="#">{!! $companyProfile[0]->website !!}</a> | 
											<a href="#">{!! $companyProfile[0]->company_email !!}</a>
										</li>
										<li class="font-weight-bold">{!! $companyProfile[0]->company_phone_no !!}</li>
									</ul>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-4" style="float:right">
									<div class="text-sm-right">
										<h2 class="text-primary mb-2 mt-md-2">Invoice No: {!! $invoice_no !!}</h2>
										<ul class="list list-unstyled mb-0">
											<li>Date: <span  class="font-weight-bold">
												<?php
													$array = explode(" ", $dateInvoiced[0]->created_at);
													echo ltrim(date('dS \of F, Y', strtotime($array[0])), '0');
												?>
											</span></li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="mb-4 mb-md-2 col-md-6">
								<p id="editClientInformation" class="text-primary-400" style="cursor:pointer">Edit <i class="icon-pencil"></i></p>
								<p id="closeEditClientInformation" class="hidden text-danger" style="cursor:pointer">Close <i class="icon-x"></i></p>
								
								<span class="font-weight-bold">Billing To:</span>
								<ul class="list list-unstyled mb-0">
									
									<div id="defaultCompanyInfo">
										<li class="font-weight-bold">
											<h5 class="my-2">
												@if(isset($invoiceBiller))
													{!! $invoiceBiller->client_name !!}
												@else
													{!! strtoupper($completedInvoice[0]->company_name) !!}
												@endif
											</h5>
										</li>
										<li class="font-weight-bold">
											@if(isset($invoiceBiller))
												{!! $invoiceBiller->client_address !!}
											@else
												{!! ucwords($completedInvoice[0]->address) !!}
											@endif
										</li>
										<li class="font-weight-bold">Nigeria</li>
									</div>
									

									<div id="editClientContainer" class="hidden">
										<li>
											<input type="text" class="form-control font-weight-bold mb-2" name="client_name" id="clientName" style="width:70%" value="@if(isset($invoiceBiller)){!! $invoiceBiller->client_name !!} @else {!! strtoupper($completedInvoice[0]->company_name) !!}@endif">
										</li>
										<li>
											<textarea class="form-control font-weight-bold" name="client_address" id="clientAddress" style="width:70%">@if(isset($invoiceBiller)) {!! $invoiceBiller->client_address !!} @else  {!! ucwords($completedInvoice[0]->address) !!} @endif</textarea>
										</li>
										<li id="clientDetailsLoader"></li>
										<li><button id="saveClientDetails" value="{{$invoice_no}}" class="mt-2 btn btn-primary">Rename</button></li>
										<input type="hidden" value="{{$invoice_no}}" name="invoice_no">
									</div>
									
								</ul>
							</div>

							<div class="mb-2  col-md-6">
								<span class="text-muted">Payment Details:</span>
								<div class="d-flex flex-wrap wmin-md-400">
									<ul class="list list-unstyled mb-0">
										<li><h6 class="my-1 font-weight-bold">Total Due:</h6></li>
										<li><h6 class="my-1 font-weight-bold">Withholding Tax (5%):</h6></li>
										<li><h6 class="my-1 font-weight-bold">Amount Payable:</h6></li>
										<li>Bank name:</li>
										<li>Account Name:</li>
										<li>Account Number:</li>
										<li>Country:</li>
										<li>Tax Identification No:</li>
									</ul>

									<ul class="list list-unstyled text-right mb-0 ml-auto">
										<li><h6 class="font-weight-bold my-1">&#x20a6;<span id="totalDue"></span></h6>
										</li>
										<li><h6 class="font-weight-bold my-1">&#x20a6;<span id="widthholdingTax"></span></h6>
										</li>
										<li><h6 class="font-weight-bold my-1">&#x20a6;<span id="amountPayable"></span></h6>
										</li>
										<li><span class="">{!! $companyProfile[0]->bank_name !!}</span></li>
										<li>{!! $companyProfile[0]->account_name !!}</li>
										<li>{!! $companyProfile[0]->account_no !!}</li>
										<li>Nigeria</li>
										<li><span >{!! $companyProfile[0]->tin !!}</span></li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr style="font-size:12px; font-family:tahoma; font-weight:bold">
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
							<tbody style="font-size:12px; font-family:tahoma">
								@if(count($completedInvoice))
									<?php
										$subtotal = 0;
										$sumtotalIncentive = 0;
										$totalVatRate = 0;
										$incentiveVatRate = 0;
									?>
									
									@foreach($completedInvoice as $invoiceParams)
									   
										<?php
										    
											$trip_id = intval(str_replace('KAID', '', $invoiceParams->trip_id));
										?>
										<?php 
											$subtotal+=$invoiceParams->client_rate;
											//change!!!
											$vat = $vatRateValue / 100 *  $invoiceParams->client_rate;
                                            $totalVatRate+=$vat;
                                            
										?>
										<tr class="font-weight-bold">
											<td class="font-weight-bold">
												<span class="defaultInfo">{!! date('d/m/Y', strtotime($invoiceParams->gated_out)) !!}</span>
												<div class="bulkyAlter hidden">
													<input type="datetime-local" value="{{ $invoiceParams->gated_out }}" style="font-size:10px; width:150px" name="gatedOut[]">
												</div>
											</td>
											<td>
											<h6 class="mb-0">
											<span class="defaultInfo">
												<a href="#">{!! $invoiceParams->customers_name  !!}</a>
												<span class="d-block font-size-sm ">Destination: 
													{!! $invoiceParams->state !!}, {!! $invoiceParams->exact_location_id !!}
												</span>
											</span>
											<div class="bulkyAlter hidden">
												<input type="text" value="{!! $invoiceParams->customers_name  !!}" name="customersName[]" style="font-size:10px; width:150px">
												<input type="text" value="{!! $invoiceParams->exact_location_id  !!}" name="exactLocation[]" style="font-size:10px; width:100px">
											</div>
											</h6>
											<input type="hidden" name="trip_id[]" value="{!! $invoiceParams->id !!}">
										</td>
										<td>
											<span class="defaultInfo">{!! $invoiceParams->product !!}</span>
											<div class="bulkyAlter hidden">
												<select style="font-size:10px; width:100px" name="product[]">
													<!-- <option value="">{!! $invoiceParams->product !!}</option> -->
													@foreach($products as $product)
														@if($product->product == $invoiceParams->product)
															<option selected value="{{$product->id}}" name="product_id[]">{{$product->product}}</option>
														@else
															<option value="{{$product->id}}">{{$product->product}}</option>
														@endif
													@endforeach
												</select>
											</div>
										</td>
										
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
										<td>
											@foreach($waybillinfos as $waybilldetails)
												@if($waybilldetails->trip_id == $invoiceParams->id)
													
													@if(isset($waybilldetails->tons))
														{!! $waybilldetails->tons !!}<br>
                                                    @else
													{!! $invoiceParams->tonnage /1000 !!}<br>
													@break
													@endif
													
												@endif
										
											@endforeach

											@foreach($incentive as $incentivedesc)
												@if($incentivedesc->trip_id == $invoiceParams->id)
													<span style="font-size:10px; text-decoration:title">{{$incentivedesc->incentive_description}}</span>
												@endif
											@endforeach
										</td>
										<td>
											<span class="initialRatePlaceholder">&#x20a6;{!! number_format($invoiceParams->client_rate, 2) !!}<br>
											@foreach($incentive as $incentivedesc)
												@if($incentivedesc->trip_id == $invoiceParams->id)
												&#x20a6;{{number_format($incentivedesc->amount, 2)}} <i class="icon-trash text-danger-400 removeIncentive" title="Remove Incentive" style="font-size:10px; cursor:pointer" id="{{$incentivedesc->id}}"></i>
												@endif
											@endforeach
											</span> 

											<span class="editClientRatePlaceholder" style="display:none">
												<input type="text" value="{{$invoiceParams->client_rate}}" name="initialAmount[]">
												<input type="text" value="{{$invoiceParams->id}}" name="tripIdListings[]">
												<input type="hidden" value="{{$invoice_no}}" id="invoiceNumber">
											</span>
										</td>
										</tr>
											@foreach($incentive as $totalIncentive)
												@if($totalIncentive->trip_id == $invoiceParams->id)
												<?php $sumtotalIncentive += $totalIncentive->amount;
												//change!!!
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

					<div class="card-body" style="font-family:tahoma; font-size:12px; ">
						<div class="row">
							<div class="pt-2 mb-3 col-md-4">
								<h6 class="mb-3">Authorized Signatory</h6>
								<div class="">
									 <img src="{{URL::asset('/assets/img/kaya/'.$companyProfile[0]->signatory)}}" width="100" alt="{{$companyProfile[0]->first_name}}"> 
								</div>

								<ul class="list-unstyled">
									<li>{!! ucfirst($companyProfile[0]->first_name) !!} {!! ucfirst($companyProfile[0]->last_name) !!}</li>
									<li>{!! $companyProfile[0]->email !!}</li>
									<li><em>For</em> : Kaya Africa Technologies Nig. Ltd</li>
								</ul>
							</div>

							<div class="pt-2 mb-3 wmin-md-400 ml-auto col-md-8">
								<div class="table-responsive">
									<table class="table">
										<tbody>
										<tr>
												<?php
													$subtotal+=$sumtotalIncentive;
													//change
													$totalVatRate = $vatRateValue / 100 * $subtotal;
												?>

												<th><b>Subtotal (Actual):</b></th>
												<td class="text-right">&#x20a6;{!! number_format($subtotal, 2) !!}</td>
											</tr>
											<tr>
												<th><b>VAT:</b> <span class="font-weight-normal">({{$vatRateValue}}%)</span></th>
												<td class="text-right">&#x20a6;{!! number_format($totalVatRate, 2) !!}</td>
											</tr>
											<!-- <tr>
												<th><b>Subtotal (Incentive):</b></th>
												<td class="text-right">&#x20a6;{!! number_format($sumtotalIncentive, 2) !!}</td>
											</tr>
											<tr>
												<th><b>VAT:</b> <span class="font-weight-normal">(7.5%)</span></th>
												<td class="text-right">&#x20a6;{!! number_format($incentiveVatRate, 2) !!}</td>
											</tr> -->
											<tr>
												<th><b>TOTAL:</b></th>
												<td class="text-right text-primary"><h5 class="font-weight-semibold">&#x20a6;{!! number_format($subtotal + $totalVatRate, 2) !!}</h5></td>
												<input id="sumtotalIncentive" type="hidden" value="{!! number_format($subtotal + $totalVatRate, 2) !!}">

											</tr>
										</tbody>
									</table>
								</div>

								<div class="text-right mt-3">
									<span id="loader"></span>

									<div class="" id="saveAndPrintContainer">

										<button type="button" id="alterInformation" class="btn btn-light btn-sm"><i class="icon-rotate mr-2"></i>Alter<i class="icon-pdf2"></i></button>

										<button type="button" id="updateAlteredInformation" class="btn btn-light btn-sm"><i class="icon-rotate mr-2"></i>Update<i class="icon-pdf2"></i></button>

										<button type="button" id="editInvoice" class="btn btn-light btn-sm ml-3"><i class="icon-pencil mr-2"></i> Edit Rate</button>

										<button type="button" class="btn btn-primary btn-sm ml-3" id="updateInvoice" style="display:none"><i class="icon-checkmark2 mr-2"></i> Done</button>

										<button type="button" class="btn btn-danger btn-sm ml-3" id="exitUpdateBtn" style="display:none"><i class="icon-x mr-2"></i>Cancel</button>

										<button type="button" id="printInvoice" class="btn btn-light btn-sm ml-3"><i class="icon-printer mr-2"></i> Print</button>

									</div>

								</div>
							</div>
						</div>
					</div>
					
					<?php
						// dont change from 5%
						$withholdingTax = $withholdingTaxValue / 100 * $subtotal;
						$total = $subtotal + $totalVatRate;
						$amountPayable = $total - $withholdingTax;
						$payables = [number_format($withholdingTax,2), number_format($amountPayable, 2)];
					?>

					

					<div class="card-footer">
						<span class="text-muted">Thank you for choosing Kaya Africa Technology Nigeria Limited as your trusted logistic partner. This invoice can be paid via Bank transfer, or any other payment means convenient.</span>
					</div>
				</div>
			</div>
			<!-- /invoice template -->



			</form>
		</div>
		<!-- /content area -->


	</div>
	<!-- /main content -->
</form>

	
	
@include('finance.invoice.partials._invoice-and-sales-update')	
	
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/print.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/invoice.js')}}"></script>
  <script>
    $(function() {
        $total = $("#sumtotalIncentive").val();
        $("#totalDue").html($total);

		$payables = <?php echo json_encode($payables);  ?>;
		$withholdingTax = $payables[0];
		$amountPayable = $payables[1];

		$('#widthholdingTax').html($withholdingTax);
		$('#amountPayable').html($amountPayable);

        $("#printInvoice").click(function() {
			$("#saveAndPrintContainer").addClass("hidden");
			$('.icon-trash').addClass('hidden');
			$('.deleteInvAndTonsplitter').addClass('hidden');
			$('#editClientInformation').addClass('hidden')
            $.print("#printableArea");
            window.setTimeout(() => {
				$("#saveAndPrintContainer").addClass("show").removeClass('hidden');
				$('.icon-trash').removeClass('hidden');	
				$('.deleteInvAndTonsplitter').removeClass('hidden');
				$('#editClientInformation').removeClass('hidden');
            }, 3000);
		});
		
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
			$.post('/update-initial-invoice-price', $('#frmReprintInvoice').serializeArray(), function(response){
				if(response == 'amountUpdated'){
					$url = `/invoice-trip/${$invoiceNumber}`;
					window.location.href=$url;
				} else {

				}
			})
		});

		$('#editClientInformation').click(function() {
			$(this).addClass('hidden');
			$('#closeEditClientInformation').removeClass('hidden')
			$('#defaultCompanyInfo').addClass("hidden");
			$('#editClientContainer').addClass('show').removeClass('hidden');
		});
		
		$('#closeEditClientInformation').click(function() {
			$(this).addClass('hidden')
			$('#editClientInformation').removeClass('hidden')
			$('#defaultCompanyInfo').removeClass("hidden");
			$('#editClientContainer').removeClass('show').addClass('hidden');
		});

		$('#saveClientDetails').click(function(e) {
			e.preventDefault();
			$invoiceNo = $(this).attr('value');
			$clientName = $("#clientName").val();
			$clientAddress = $('#clientAddress').val();
			$('#clientDetailsLoader').html('<i class="spinner icon-spinner mt-2"></i> please wait...')
			$.post('/invoice-biller', $('#frmReprintInvoice').serializeArray(), function(data) {
				if(data == 'changed') {
					$('#clientDetailsLoader').html('The biller name changed successfully.');
					window.location = '';
				}
				else{
					$('#clientDetailsLoader').html('Oops! Something went wrong.');
					return false;
				}
			})
		}) 
		

		$('#alterInformation').click(function() {
			//defaultInfo bulkyAlter
			$('.bulkyAlter').removeClass('hidden')
			$('.defaultInfo').addClass('hidden')
			$(this).addClass('hidden')
			$('#updateAlteredInformation').removeClass('hidden')
		})

		$('#updateAlteredInformation').click(function() {
			$('.bulkyAlter').addClass('hidden')
			$('.defaultInfo').removeClass('hidden')
			$(this).addClass('hidden')
			$('#alterInformation').removeClass('hidden');
			$('#loader').html('<i class="spinner icon-spinner mt-2"></i> please wait...')
			
			$.post('/alter-trip-information', $('#frmReprintInvoice').serialize(), function(data) {
				if(data === 'updated') {
					$('#loader').html('Updated successfully.');
					window.location='';
				}
				else {
					$('#loader').html('<i class="spinner icon-spinner mt-2"></i>Something went wrong, please contact the administrator')
					return false;
				}
			})
		})

    });
</script>
@stop

<!--   -->