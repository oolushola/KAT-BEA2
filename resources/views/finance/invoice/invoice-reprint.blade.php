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
			$withholdingTaxValue_ = $vatRateInfos->withholding_tax_used;
			$vatRateValue_ = $vatRateInfos->vat_used;
			if(!isset($withholdingTaxValue_) && !isset($vatRateValue_)) {
				$withholdingTaxValue = 5;
				$vatRateValue = 7.5;
			}
			else{
				$withholdingTaxValue = $withholdingTaxValue_;
				$vatRateValue = $vatRateValue_;
			}
			
		?>
		<div class="content">
			<form id="frmReprintInvoice" method="POST">
				<?php
					$payment_status = $paidStatus[0]->paid_status;
				?>
				@csrf
			<!-- Invoice template -->
			<div id="printableArea">
				<div class="card">
					<div class="card-header bg-transparent header-elements-inline">
                        <h6 class="card-title"><?php 
                            $array = explode(" ", $dateInvoiced[0]->created_at);
						?>
						<button type="button" data-toggle="modal" href="#addMoreTrips" class="btn btn-primary font-size-sm font-weight-semibold hideOnPrint" >ADD MORE</button>&nbsp;

						<button type="button" class="btn btn-primary font-size-sm font-weight-semibold hideOnPrint" data-toggle="modal" href="#salesOrderAndInvoiceNumber">CHANGE INVOICE NO, S.O NUMBER</button>
						
					</h6>
						
						<div class="deleteInvAndTonsplitter">
							@if($completedInvoice[0]->company_name == 'Olam International')
								@if($po_number->po_number != "")
									<span class="font-weight-semibold font-size-md updatePoNumber" id="displayPo">PO NUMBER: {{ $po_number->po_number }}</span>
								@else
									<span class="font-weight-semibold font-size-md text-danger mr-2 updatePoNumber">ADD NEW PO</span>
								@endif


								<input type="text" placeholder="ENTER PO NUMBER" style="font-size:11px; padding:3px; font-family:tahoma" id="updatePoNumber" data-id="{{ $invoice_no }}" class="d-none" value="{{ $po_number->po_number }}"  />
								<span id="poLoader"></span>
								
							@endif
							<button type="button" class="hideOnPrint btn btn-danger deleteInvoice font-size-sm font-weight-semibold" value="{{$invoice_no}}">DELETE INVOICE</button>
						</div>
					</div>

					<div class="card-body" style="font-family:tahoma; font-size:12px;">
						<div class="row">
							<div class="col-md-6">
								@if($completedInvoice[0]->company_name == 'APMT')
								<a href="{{URL('invoice-collage/'.$invoice_no)}}">Collage This Invoice</a>
								@endif

								<div class="mb-4">
									<img src="{{URL::asset('/assets/img/kaya/'.$companyProfile[0]->company_logo)}}" class="mb-3 mt-2" alt="company's Logo" style="width: 70px;">
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-4" style="float:right">
									<div class="text-sm-right">
										<span id="defaultInvoiceNoAndDate">
											<h2 class="text-primary mb-2 mt-md-2">Invoice No: {!! $invoice_no !!}</h2>
											<ul class="list list-unstyled mb-0">
												<li>Date: <span  class="font-weight-bold">
													<?php
														$array = explode(" ", $dateInvoiced[0]->created_at);
														echo ltrim(date('dS \of F, Y', strtotime($array[0])), '0');
													?>
												</span></li>
											</ul>
										</span>
										<span id="alterInvoiceNoAndDate" class="hidden">
											<h2 class="text-primary mb-2 mt-md-2">Invoice No: <input type="text" value="{{ $invoice_no }}" id="completeInvoiceNo" class="changeInvNoDate"></h2>
											<span class="d-block">Date: <input type="datetime-local" value="{{ date('Y-m-d\TH:i') }}" id="dateInvoicedCompleted" class="changeInvNoDate">
												<input type="hidden" value="{{ $invoice_no }}" id="previousInvoiceNo">
											</span>
											<span class="d-block" id="dateAndInvoicePlaceholder"></span>
										</span>
									</div> 
								</div>
							</div>
						</div>

						<div class="row">
							<div class="mb-4 mb-md-2 col-md-6">
								<p id="editClientInformation" class="text-primary-400 hideOnPrint" style="cursor:pointer">Edit <i class="icon-pencil"></i></p>
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
											<select name="client_name" id="clientName" style="width:70%" class="form-control font-weight-bold mb-2">
												<option value="">Choose Who You are billing it to.</option>
												@foreach($clients as $client)
												@if($completedInvoice[0]->company_name == $client->company_name)
												<option value="{{$client->company_name}}" selected> {{ $client->company_name }} </option>
												@else
												<option value="{{$client->company_name}}"> {{ $client->company_name }} </option>
												@endif
												@endforeach
											</select>
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
										
										<li>@if($preferedBankDetails->bank_name_payment) <span class="">{!! $preferedBankDetails->bank_name_payment !!}</span>
											@else <span class="">{!! $companyProfile[0]->bank_name !!}</span> @endif
										</li>
										<li>@if($preferedBankDetails->account_name_payment) <span class="">{!! $preferedBankDetails->account_name_payment !!}</span>
											@else <span class="">{!! $companyProfile[0]->account_name !!}</span> @endif
										</li>
										<li>@if($preferedBankDetails->account_no_payment) <span class="">{!! $preferedBankDetails->account_no_payment !!}</span>
											@else <span class="">{!! $companyProfile[0]->account_no !!}</span> @endif
										</li>
										<li>Nigeria</li>
										<li><span >{!! $companyProfile[0]->tin !!}</span></li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-striped" >
							<thead>
								<tr style="font-size:12px; font-family:tahoma; font-weight:bold" >
									<th><b>Invoice Date</b></th>
									<th><b>Customer</b></th>
									<th><b>Product</b></th>
									<th><b>Truck No.</b></th>
									<th><b>@if(isset($invoiceHeadings)){{$invoiceHeadings->sales_order_no_header}}@else{{'S.O. No.'}}@endif</b></th>
									<th><b>@if(isset($invoiceHeadings)){{$invoiceHeadings->invoice_no_header}}@else{{'Waybill No.'}}@endif</b></th>
									<th width="10%"><b>Ton</b></th>
									<th><b>Rate</b> </th>
									@if(!$payment_status && count($completedInvoice) > 1)
									<th class="specificTripRemove hideOnPrint" id="removeLoader"></th>
									@endif
								</tr>
							</thead>
							<tbody style="font-size:12px; font-family:tahoma">
								@if(count($completedInvoice))
									<?php
										$subtotal = 0;
										$sumtotalIncentive = 0;
										$totalVatRate = 0;
										$incentiveVatRate = 0;
										$sumTotalSpecialRemark = 0;
										$specialRemarkCondition = "+";
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
												<span class="text-primary">{!! ucwords($invoiceParams->customers_name) !!}</span>
												<span class="d-block font-size-sm ">Destination: 
													{!! trim($invoiceParams->state) !!}, {!! $invoiceParams->exact_location_id !!}
												</span>
											</span>
											<div class="bulkyAlter hidden">
												<input type="text" value="{!! $invoiceParams->customers_name  !!}" name="customersName[]" style="font-size:10px; width:150px">
												<input type="text" value="{!! $invoiceParams->exact_location_id  !!}" name="exactLocation[]" style="font-size:10px; width:100px">
											</div>
											</h6>
											<input type="hidden" name="trip_id[]" value="{!! $invoiceParams->id !!}">

											@foreach($incentives as $key => $availableIncentive)
												@if($invoiceParams->exact_location_id == $availableIncentive->exact_location)
												<span class="font-size-sm hideOnPrint">
													<button id="{{$availableIncentive->id}}" value="{{ $invoiceParams->id }}" class="addSpecificIncentive" style="font-size:10px; border:1px solid #000; border-radius:5px; cursor:pointer; padding:5px;">Incentive of <strong>&#x20a6;{{number_format($availableIncentive->amount,2)}}</button>
												</span>
												@endif
												
											@endforeach
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
										
										<td>{!! str_replace(' ', '', $invoiceParams->truck_no) !!}</td>
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
												&#x20a6;{{number_format($incentivedesc->amount, 2)}} 
												
													@if(!$payment_status)
													<i class="icon-trash hideOnPrint text-danger-400 removeIncentive" title="Remove Incentive" style="font-size:10px; cursor:pointer" id="{{$incentivedesc->id}}"></i>
													@endif

												@endif
											@endforeach
											</span> 

											<span class="editClientRatePlaceholder" style="display:none">
												<input type="text" value="{{$invoiceParams->client_rate}}" name="initialAmount[]">
												<input type="hidden" value="{{$invoiceParams->id}}" name="tripIdListings[]">
												<input type="hidden" value="{{$invoice_no}}" id="invoiceNumber">
											</span>
										</td>
										@if(!$payment_status && count($completedInvoice) > 1)
									
										<td class="specificTripRemove hideOnPrint">
											<i class="icon icon-minus-circle2 removeSpecificTrip text-danger" title="Remove this trip with ID: {{ $invoiceParams->trip_id  }}" id="{{$invoiceParams->id}}" style="cursor:pointer"></i>
										</td>
									
										@endif


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
										@if(isset($invoiceSpecialRemark))
										<?php $invoiceSpecialRemark->condition == '+' ? $class = 'text-primary' : $class = 'text-danger'; ?>
										<tr class="font-weight-bold {{$class}} ">
											<td colspan="3"></td>
											<td colspan="4">{{ $invoiceSpecialRemark->description }}</td>
											<td>&#x20a6;{{ number_format($invoiceSpecialRemark->amount, 2) }}</td>
											<?php $sumTotalSpecialRemark = $invoiceSpecialRemark->amount;
												$specialRemarkCondition = $invoiceSpecialRemark->condition;
											?>
										</tr>
										@endif
								@else
									<tr>
										<td colspan="10">You didnt select any trip for invoicing</td>
									</tr>
								@endif
								
							</tbody>

						</table>

					</div>

					<div id="specialRemark" class="font-size-sm mt-3 mb-3 hideOnPrint">
						<input type="checkbox" id="specialRemarkChecker" class="ml-4"><span class="descriptor-label font-weight-semibold text-danger">Use Special Remark For this Invoice</span>
						<span class="descriptor hidden">
							<select name="condition" id="condition">
								<option value="">Choose Criteria for Special Remark</option>
								<option value="+">Add-on (Incentive/Extra/Overpay)</option>
								<option value="-">Deduction (Underpay)</option>
							</select>
							<input type="text" placeholder="Description" name="description" id="description">
							<input type="text" placeholder="Amount" name="amount" id="amount">
							<button id="saveSpecialRemark" class="btn btn-warning">Save</button>
						</span>

					</div>

					<div class="card-body" style="font-family:tahoma; font-size:12px; ">
						<div class="row">
							<div class="pt-2 mb-3 col-md-4">
								<h6 class="mb-3">Authorized Signatory</h6>
								<div class="">
									 <img src="{{URL::asset('/assets/img/kaya/'.$companyProfile[0]->signatory)}}" width="100" alt="{{$companyProfile[0]->first_name}}"> 
								</div>

								<ul class="list-unstyled" style="position:relative; z-index:1000;">
									<li>{!! ucfirst($companyProfile[0]->first_name) !!} {!! ucfirst($companyProfile[0]->last_name) !!}</li>
									<li>{!! $companyProfile[0]->email !!}</li>
									<li><em>For</em> : Kaya Africa Technologies Nig. Ltd</li>
								</ul>
								<img src="{{URL('assets/img/official-stamp.png')}}" alt="auto-stamp" width="250" height="70" style="position:relative; top:-30px; z-index:12; left:-20px;">
							</div>

							<div class="pt-2 mb-3 wmin-md-400 ml-auto col-md-8">
								<div class="table-responsive">
									<table class="table">
										<tbody>
										<tr>
												<?php
													
													if(isset($invoiceSpecialRemark)){
														if($invoiceSpecialRemark->condition == "+"){
															$subtotal+=$sumtotalIncentive + $sumTotalSpecialRemark;
														} else {
															$subtotal+=$sumtotalIncentive - $sumTotalSpecialRemark;
														}
													} else{
														$subtotal+=$sumtotalIncentive + $sumTotalSpecialRemark;
													}
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

									<div class="hideOnPrint">

										<button type="button" id="alterInformation" class="btn btn-light btn-sm"><i class="icon-rotate mr-2"></i>Alter<i class="icon-pdf2"></i></button>

										<button type="button" id="updateAlteredInformation" class="btn btn-light btn-sm hidden"><i class="icon-rotate mr-2"></i>Update<i class="icon-pdf2"></i></button>

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
						<!-- <span class="text-muted">Thank you for choosing Kaya Africa Technology Nigeria Limited as your trusted logistic partner. This invoice can be paid via Bank transfer, or any other payment means convenient.</span> -->
						<p class="text-center text-muted m-0 font-weight-semibold">{!! $companyProfile[0]->address !!} Lagos, Nigeria | {!! $companyProfile[0]->website !!}</p>
						<p class="text-center text-muted m-0 font-weight-semibold">{!! $companyProfile[0]->company_email !!} | Tel: {!! $companyProfile[0]->company_phone_no !!}</p>
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
@include('finance.invoice.partials._add-more-trip-to-invoice')
	
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
			$(".hideOnPrint").addClass("hidden");
             $.print("#printableArea");
            window.setTimeout(() => {
				$(".hideOnPrint").addClass("show").removeClass('hidden');
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

		//change client name to get address
		$('#clientName').change(function() {
			$.get('/get-client-address', $('#frmReprintInvoice').serializeArray(), function(data) {
				$("#clientAddress").val(data);
			});
		})

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

		// Add specific incentive
		$('.addSpecificIncentive').click(function($event) {
			$event.preventDefault();
			$incentiveId = $(this).attr('id')
			$tripId = $(this).attr('value')
			$(this).html('<i class="icon-spinner2 spinner"></i> Updating...')
			$btn = $(this)
			$.get('/update/invoice-incentive', { incentive_id: $incentiveId, trip_id: $tripId }, function(data) {
				if(data === 'added') {
					$btn.html('<i class="icon-checkmark2"></i> Updated')
				}
			})
		})

		// Make invoice no and date editable
		$('#defaultInvoiceNoAndDate').dblclick(function(e) {
			e.preventDefault()
			$(this).addClass('hidden');
			$('#alterInvoiceNoAndDate').removeClass('hidden')
			$('#completeInvoiceNo').focus()
		})

		$('.changeInvNoDate').keypress(function(e) {			
			$invoiceNo = $('#completeInvoiceNo').val();
			$dateInvoice = $('#dateInvoicedCompleted').val();
			$previousInvoiceNo = $('#previousInvoiceNo').val();
			if(e.keyCode === 13) {
				e.preventDefault();
				$('#dateAndInvoicePlaceholder').html('<i class="icon-spinner2 spinner"></i> Please wait...')
				$.get('/update-invoice-number-and-date', { complete_invoice_no: $invoiceNo, date_invoiced: $dateInvoice, previos_invoice_no: $previousInvoiceNo}, function(data) {
					if(data === 'invoiceNoExists') {
						$('#dateAndInvoicePlaceholder').html('<i class="icon-x"></i>Wrong!!! This invoice no is in use.')						
						return false
					}
					else {
						if(data === 'updated') {
							$url = '/invoice-trip/'+$invoiceNo;
							window.location.href = $url;
						}
					}

				})
			}
			else {
				if (e.keyCode == 27) {
					$('#defaultInvoiceNoAndDate').removeClass('hidden')
					$('#alterInvoiceNoAndDate').addClass('hidden')
				}
			}
			
		})
		
		$('#updatePoNumber').keypress(function(e) {
			$invoiceNo = $(this).attr('data-id')
			$poNumber = $(this).val()
			if(e.keyCode === 13) {
				e.preventDefault()
				$('#poLoader').html('<i class="spinner icon-spinner3"></i>Updating PO NUmber...')
				$e = $(this)
				$.get('/update-po-number', { invoice_no: $invoiceNo, po_number: $poNumber }, function(data) {
					if(data === 'updated') {
						$('#poLoader').html('<i class="icon-checkmark2"></i>').fadeIn(1000).delay(2000).fadeOut(5000)
						$e.addClass('d-none')
						$('#displayPo').html('PO Number: '+$poNumber).removeClass('d-none')
					}
				})
			}
			else{
				if(e.keyCode === 27) {
					$(this).addClass('d-none')
				}
			}
		})

		$('.updatePoNumber').dblclick(function() {
			$(this).addClass('d-none')
			$('#updatePoNumber').removeClass('d-none')
		})
	});

	
	
</script>
@stop

<!--   -->