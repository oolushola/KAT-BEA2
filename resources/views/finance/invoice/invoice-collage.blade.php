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
				?>
				@csrf
			<!-- Invoice template -->
			<div id="printableArea">
				<div class="card">

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
										<span id="defaultInvoiceNoAndDate">
											<h2 class="text-primary mb-2 mt-md-2">Invoice No: {!! $invoice_no !!}</h2>
											<ul class="list list-unstyled mb-0">
												<li>Date: <span  class="font-weight-bold">
													<?php
														$array = explode(" ", $dateInvoiced->created_at);
														echo ltrim(date('dS \of F, Y', strtotime($array[0])), '0');
													?>
												</span></li>
											</ul>
										</span>
										
									</div> 
								</div>
							</div>
						</div>

						<div class="row">
							
                        <div class="mb-4 mb-md-2 col-md-6">
							<span class="font-weight-bold">Billing To:</span>
                            <ul class="list list-unstyled mb-0">
                                
                                <div id="defaultCompanyInfo">
                                    <li class="font-weight-bold">
                                        <h5 class="my-2"> {!! $biller[0]->company_name !!} </h5>
                                    </li>
                                    <li class="font-weight-bold"> {!! ucwords($biller[0]->address) !!} </li>
                                    <li class="font-weight-bold">Nigeria</li>
                                </div>
                            </ul>
                        </div>
                            
							<div class="mb-2  col-md-6">
								<span class="text-muted">Payment Details:</span>
								<div class="d-flex flex-wrap wmin-md-400">
									<ul class="list list-unstyled mb-0">
										<li>
                                            <h6 class="my-1 font-weight-bold">Total Due:</h6>
                                        </li>
										<li>
                                            <h6 class="my-1 font-weight-bold">Withholding Tax (5%):</h6>
                                        </li>
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
										
										<li><span class="">{!! $companyProfile[0]->bank_name !!}</span> </li>
										<li><span class="">{!! $companyProfile[0]->account_name !!}</span></li>
										<li><span class="">{!! $companyProfile[0]->account_no !!}</span></li>
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
									<th><b>Origin</b></th>
									<th><b>Customer</b></th>
									<th class="text-center"><b>Size</b></th>
                                    <th class="text-center"><b>No of Unit</b></th>
                                    <th class="text-center"><b>Rate</b></th>
                                    <th class="text-center"><b>Total</b></th>									
								</tr>
							</thead>
                            <tbody style="font-size:12px; font-family:tahoma">
                            <?php 
                                $subtotal = 0;
                            ?>
                            @foreach($tripsOrginRateAndWeight as $keys => $originRateAndWeight)
                                <tr class="font-weight-bold">
                                    <td>{{ $originRateAndWeight->loading_site }}</td>
                                    <td>
                                        <h6 class="mb-0 text-primary">APMT KANO</h6>
                                        <span class="d-block font-size-sm">Destination: Kano, Kano</span>    
                                    </td>
                                    <td class="text-center">{{ $originRateAndWeight->loaded_weight }}</td>
                                    <td class="text-center">{{ $noOfUnits[$keys]->no_of_unit }}</td>
                                    <td class="text-center">{{ number_format($originRateAndWeight->client_rate, 2) }}</td>
                                    <td class="text-center">
                                        {{ number_format($noOfUnits[$keys]->no_of_unit * $originRateAndWeight->client_rate, 2) }}
                                    </td>
                                </tr>

                                <?php $subtotal +=  $noOfUnits[$keys]->no_of_unit * $originRateAndWeight->client_rate; ?>
                            @endforeach

                                <?php 
                                    $vat = $vatRateValue / 100 * $subtotal; 
                                    $total = $subtotal + $vat;
                                    $withholdingTax = $withholdingTaxValue / 100 * $subtotal;
                                    $amountTobePaid = $total - $withholdingTax;
                                ?>
                                
							</tbody>

						</table>

					</div>

					

					<div class="card-body" style="font-family:tahoma; font-size:12px; ">
						<div class="row">
							<div class="pt-2 mb-3 col-md-6">
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

							<div class="pt-2 mb-3 wmin-md-400 ml-auto col-md-6">
								<div class="table-responsive">
									<table class="table">
										<tbody>
										    <tr>
												<th><b>Subtotal (Actual):</b></th>
												<td class="text-right">&#x20a6;{!! number_format($subtotal, 2) !!}</td>
											</tr>
											<tr>
												<th><b>VAT:</b> <span class="font-weight-normal">({{$vatRateValue}}%)</span></th>
												<td class="text-right">&#x20a6;{!! number_format($vat, 2) !!}</td>
											</tr>
											<tr>
												<th><b>TOTAL:</b></th>
												<td class="text-right text-primary"><h5 class="font-weight-semibold">&#x20a6;{!! number_format($total, 2) !!}</h5></td>
                                                
                                                <input type="hidden" value="{{ number_format($total, 2) }}" id="sumTotal" />
                                                <input type="hidden" value="{{ number_format($withholdingTax, 2) }}" id="taxWithheld" />
                                                <input type="hidden" value="{{ number_format($amountTobePaid, 2) }}" id="amounttoreceived" />

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

	
	
	
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/print.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/invoice.js')}}"></script>
  <script>
    $(function() {
        $total = $("#sumTotal").val();
        $("#totalDue").html($total);
        $('#widthholdingTax').html($('#taxWithheld').val())
        $('#amountPayable').html($('#amounttoreceived').val());

        $("#printInvoice").click(function() {
			$(".hideOnPrint").addClass("hidden");
             $.print("#printableArea");
            window.setTimeout(() => {
				$(".hideOnPrint").addClass("show").removeClass('hidden');
            }, 3000);
		});
	
		
	});

	
	
</script>
@stop

<!--   -->