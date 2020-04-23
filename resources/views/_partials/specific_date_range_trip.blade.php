<!-- Modal HTML for Advance Request -->
<div id="specificDateRangeInformation" class="modal fade">
        <div class="modal-dialog" >
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">Spefic Trip Range</h5>
                    <span class="ml-2"></span>
                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                    
                </div>
                
                <div class="modal-body">
                   <div style="padding:10px;"><input type="text" class="form-control" id="searchCurrentGateOut" placeholder="SEARCH"></div>
                   <div class="row" id="specificDataRangeRecord">

                        <table class="table table-striped table-hover">
                            <thead class="table-success" style="font-size:10px;">
                                <tr>
                                    <th width="20%" class="text-center font-weight-bold">GATE OUT DETAILS</th>
                                    <th width="30%" class="font-weight-bold">TRUCK</th>
                                    <th width="20%" class="font-weight-bold">WAYBILL DETAILS</th>
                                    <th width="30%" class="font-weight-bold">CONSIGNEE DETAILS</th>
                                </tr>
                                <tbody id="currentGateOutData" style="font-size:10px;">
                                    @if(count($specificDataRecord))
                                        @foreach($specificDataRecord as $specificRecord)
                                        <tr>
                                            <td class="text-center">
                                                
                                                <p class="font-weight-bold" style="margin:0">{{ $specificRecord->trip_id }}</p>
                                                <p>{{ $specificRecord->loading_site }} <br> {{ date('d-m-Y', strtotime($specificRecord->gated_out)) }} <br> {{ date('h:i A', strtotime($specificRecord->gated_out)) }}</p>
                                            </td>
                                            <td>
                                                <span class="text-primary"><b>{{$specificRecord->truck_no}}</b></span>
                                                <p style="margin:0"><b>Truck Type</b>: {{$specificRecord->truck_type}} {{$specificRecord->tonnage / 1000}}T</p>
                                                <p style="margin:0"><b>Transporter</b>: {{$specificRecord->transporter_name}}, {{$specificRecord->phone_no}}</p>
                                            </td>
                                            
                                            <td>
                                                @foreach($tripWaybills as $tripWaybill)
                                                    @if($specificRecord->id == $tripWaybill->trip_id)
                                                    <span class="mb-2"><a href="{{URL::asset('assets/img/waybills/'.$tripWaybill->photo)}}" target="_blank" title="View waybill {{$tripWaybill->sales_order_no}}">{{$tripWaybill->sales_order_no}}
                                                    {{$tripWaybill->invoice_no}}</a></span>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <p class="font-weight-bold" style="margin:0">{{$specificRecord->customers_name}}</p>
                                                <p  style="margin:0">Location: {{$specificRecord->exact_location_id}}</p>
                                                <p  style="margin:0">Product: {{ $specificRecord-> product}}</p>

                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="4">No record is available.</td></tr>
                                    @endif
                                    
                                    
                                </tbody>
                            </thead>
                        </table>
                       
                       

                       
                       
                   </div>
                </div>

            </div>
        </div>  
    </form>
</div>


