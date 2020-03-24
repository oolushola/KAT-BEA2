<!-- Modal HTML for Advance Request -->
<div id="totalGateOutForTheMonth" class="modal fade" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">GATED OUT FOR {{ strtoupper(date('F')) }}</h5>
                    <span class="ml-2"></span>
                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                    
                </div>
                
                <div class="modal-body">
                  <div style="padding:10px;"><input type="text" class="form-control" id="searchGatedOut" placeholder="SEARCH"></div>
                   <div class="row">

                        <table class="table table-striped table-hover">
                            <div class="table-responsive">
                            <thead>
                                <tr class="table-success">
                                    <th>SN</th>
                                    <th>GATE OUT DETAILS</th>
                                    <th>TRUCK</th>
                                    <th>WAYBILL DETAILS</th>
                                    <th>CONSIGNEE DETAILS</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyGatedOutData" style="font-size:10px; font-family:tahoma">
                                <?php $count = 1; ?>
                                @if(count($tripRecordsForTheMonth))
                                    @foreach($tripRecordsForTheMonth as $gateOutForTheMonth)
                                    <tr>
                                        <td>({{ $count++ }})</td>
                                        <td>
                                            <p class="font-weight-bold" style="margin:0">{{ $gateOutForTheMonth->trip_id }}</p>
                                            <p>{{$gateOutForTheMonth->loading_site}} <br>{{date('d-m-Y', strtotime($gateOutForTheMonth->gated_out))}}<br>{{date('H:i A', strtotime($gateOutForTheMonth->gated_out))}}</p>
                                        </td>
                                        <td>
                                            <span class="text-primary"><b>{{$gateOutForTheMonth->truck_no}}</b></span>
                                            <p style="margin:0"><b>Truck Type</b>: {{ $gateOutForTheMonth->truck_type }} {{ $gateOutForTheMonth->tonnage / 1000 }}T</p>
                                            <p style="margin:0"><b>Transporter</b>: {{$gateOutForTheMonth->transporter_name}}, {{$gateOutForTheMonth->phone_no}}</p>
                                        </td>
                                        
                                        <td><p style="margin:0" class="font-weight-bold">
                                            @foreach($tripWaybills as $tripWaybill)
                                                @if($gateOutForTheMonth->id == $tripWaybill->trip_id)
                                                <a href="{{URL::asset('assets/img/waybills/'.$tripWaybill->photo)}}" target="_blank" title="View waybill {{$tripWaybill->sales_order_no}}" >
                                                    <p class="mb-1">{{$tripWaybill->sales_order_no}} {{$tripWaybill->invoice_no}}</p>
                                                </a>
                                                @endif
                                            @endforeach
                                        </p>
                                            
                                        </td>
                                        <td>
                                            
                                            <p class="font-weight-bold" style="margin:0">{{ $gateOutForTheMonth->customers_name }}</p>
                                            <p  style="margin:0">Location: {{ $gateOutForTheMonth->exact_location_id }}</p>
                                            <p  style="margin:0">Product: {{ $gateOutForTheMonth->product }}</p>

                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">No trip is available</td>
                                    </tr>
                                @endif
                            </tbody>
                            </div>
                            
                        </table>
                       
                       

                       
                       
                   </div>
                </div>

            </div>
        </div>  
    </form>
</div>


