<!-- Modal HTML for Advance Request -->
<div id="addMoreTrips" class="modal fade">
    <form  method="POST" name="frmAddMore" id="frmAddMore" action="{{URL('/add-more-trip-to-invoice')}}">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content" style="width:1000px; margin-right:100px; position:relative; right:110px;">
                <div class="modal-header">
                    <h5 class="font-weight-bold">INVOICE NUMBER: {{ $invoice_no}}
                    <input type="hidden" name="invoice_no" value="{{$invoice_no}}">
                        
                    <input type="text" id="invoiceSearchBox" style="padding:10px; top:30px; width:200px; margin-top:10px;  margin-right:10px; height:40px; outline:none;" placeholder="Quick Search">

                    <button type="submit" class="btn btn-primary" id="invoiceTrip">ADD TO THIS INVOICE</button>


                    </h5>

                    
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <span id="exceptionLoader"></span>

                    <div class="table-responsive" id="contentLoader">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">TRIP ID</th>
                                <th>CUSTOMER</th>
                                <th>PRODUCT</th>
                                <th>TRUCK NO.</th>
                                <th class="text-center">S.O. Number</th>
                                <th class="text-center">CHOOSE</th>
                            </tr>
                        </thead>
                        <tbody id="searchAvailableInvoices">
                            <?php
                                $totalAmount = 0;
                                $totalVatRate = 0;
                            ?>
                            @if(count($invoiceList))
                                @foreach($invoiceList as $invoice)
                                <?php
                                    $totalAmount+=$invoice->client_rate;
                                    $vatRate = 5 / 100 * $invoice->client_rate;
                                    $totalVatRate+=$vatRate;
                                ?>
                                <tr>
                                    <td class="text-center font-weight-bold" width="15%">{!! $invoice->trip_id !!} <br> {!! date('d-m-Y', strtotime($invoice->gated_out)) !!}</td>
                                    <td>
                                        <h6 class="mb-0">
                                            <a href="#">{!! $invoice->customers_name  !!}</a>
                                            <span class="d-block font-size-sm text-muted">Destination: 
                                                {!! $invoice->state !!}, {!! $invoice->exact_location_id !!}
                                            </span>
                                        </h6>
                                    </td>
                                    <td>{!! $invoice->product !!}</td>
                                    <td><span class="badge badge-primary">{!! $invoice->truck_no !!}</span></td>
                                    <td class="text-center">
                                        @foreach($waybillinfos as $salesOrderNumber)
                                            @if($salesOrderNumber->trip_id == $invoice->id)
                                                {!! $salesOrderNumber->sales_order_no !!}<br>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="text-center"><input type="checkbox" name="trips[]" value="{{ $invoice->id }}"></td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="5">Precise Total Rate and Vat, exclusive of incentive rate</td>
                                    <td style="background:#000; color:#fff">
                                        <h6 class="mb-0 font-weight-bold">
                                            &#x20a6;{!! number_format($totalAmount, 2) !!}    
                                            <span class="d-block font-size-sm text-muted font-weight-normal">
                                            VAT: &#x20a6;{!! number_format($totalVatRate, 2) !!}
                                            </span>
                                        </h6>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="9">No waybill available for invoicing</td>
                                </tr>
                            @endif
                            
                            

                            

                        </tbody>
                    </table>
                </div>
                    

                    
                </div>

                

            </div>
        </div>  
    </form>
</div>
