<!-- Modal HTML for Advance Request -->
<div id="salesOrderAndInvoiceNumber" class="modal fade">
    <form method="POST" name="frmAdvanceEception" id="frmAdvanceEception" action="{{URL('update-waybill-invoice')}}">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="font-weight-sm">UPDATE CORRECT WAYBILL INFORMATION</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <span id="exceptionLoader"></span>
                    

                    <div class="row ml-3 mr-3 show" id="percentHolder">
                        @foreach($trucksAndKaidArray as $trucksAndKaid)
                            <div class="col-md-12"><h4>{{$trucksAndKaid->truck_no}}</h4></div>
                            
                            @foreach($waybillinfos as $waybill)
                                @if($waybill->trip_id === $trucksAndKaid->id)
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="totalAmountHolder" placeholder="Sales Order Number" value="{{$waybill->sales_order_no}}" name="salesOrderNumber[]">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="newAdvanceRate" min="1" max="99" placeholder="Invoice Number"  name="invoiceNumber[]" value="{{$waybill->invoice_no}}">
                                            <span id="advanceRatePreview"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" placeholder="Tons" class="form-control" name="tonnage[]" id="" min="1" ="99" value="{{$waybill->tons}}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="waybillIdListings[]" value="{{$waybill->id}}" >
                                @endif
                            @endforeach
                        @endforeach
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-semibold">&nbsp; &nbsp;</label><br>
                                <button type="submit" class="btn btn-lg btn-primary" id="updateWaybillAndInvoiceNumber">Update Changes</button>
                            </div>
                        </div>

                    </div>
                </div>

                

            </div>
        </div>  
    </form>
</div>
