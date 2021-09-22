<!-- Modal HTML for Advance Request -->
<div id="myCurrentMonthGateOut" class="modal fade">
    <form method="POST" id="frmUpdateClientRate">
        @csrf
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">Total Gate Out: as at {{ date('d-m-Y') }}</h5>
                    <span class="ml-2"></span>

                    <!-- <input type="text" class="" id="searchWaybillStatus" placeholder="SEARCH"> -->

                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                    
                </div>
                
                <div class="modal-body">
                    <div class="row table-responsive">
                        <table class="table table-condensed">
                            <thead class="table-success">
                                <tr class="font-weight-bold bg-success" style="font-size:10px;" >
                                    <td>SN</td>
                                    <td class="text-center">KAID</td>
                                    <td class="text-center" width="10%">
                                        <button class="btn btn-sm btn-primary d-none hideTruckUpdate" id="updateTruckNo">Update</button>
                                        <span class="defaultTruckDisplay">TRUCK NO</span>
                                        <span><i class="icon-lock2 defaultTruckDisplay ml-2 pointer" id="showTrucks"></i></span>
                                        <span><i class="icon-unlocked2 hideTruckUpdate d-none ml-2 pointer" id="hideTruck"></i></span>
                                    </td>
                                    <td>LOADING SITE</td>
                                    <td>
                                        <span id="destinationTitle">DESTINATION</span>
                                        <button class="btn btn-primary hidden" id="submitExactLocation">Save Changes</button>
                                        <span><i class="icon-lock2 ml-2 pointer" id="unlockDestination"></i></span>
                                        <span><i class="icon-unlocked2 hidden ml-2 pointer" id="lockDestination"></i></span>
                                    </td>
                                    <td>WAYBILL INFO</td>
                                    <td>
                                        <span id="clientRateTitle">CLIENT RATE</span>
                                        <button class="btn btn-primary hidden" id="submitClientRate">Save Changes</button> 
                                        <i class="icon-lock5" title="Change Title" style="cursor:pointer" id="unlockClientRate"></i>
                                        <i class="icon-unlocked2 hidden ml-1" title="Change Title" style="cursor:pointer" id="lockClientRate"></i>
                                    </td>
                                    <td>
                                        <span id="transporterRateTitle">TRANSPORTER RATE</span>
                                        <button class="btn btn-primary hidden" id="submitTransporterRate">Save Changes</button> 
                                        <i class="icon-lock5" title="Change Title" style="cursor:pointer" id="unlockTransporterRate"></i>
                                        <i class="icon-unlocked2 hidden ml-1" title="Change Title" style="cursor:pointer" id="lockTransporterRate"></i>
                                    </td>
                                    <td>MARGIN</td>
                                    <td>MARKUP(%)</td>
                                </tr>
                                <tbody id="currentGateOutData">
                                    <?php $count = 0; $extremeWaybillValuation = 0; ?>
                                    @if(count($currentMonthData))
                                        @foreach($currentMonthData as $specificRecord)
                                        <input type="text" class="hidden" value="{{$specificRecord->trip_id}}" name="tripListings[]">
                                        <tr style="font-size:11px;" class="font-weight-semibold">
                                            <td>({{ $count+=1 }})</td>
                                            <td style="padding:0;" class="text-center">{{ $specificRecord->trip_id }}</td>
                                            <td class="text-center">
                                                <span class="defaultTruckDisplay">{{ $specificRecord->truck_no }}</span>

                                                <select name="truckNos[]" class="d-none hideTruckUpdate form-control">
                                                    @foreach($accountOfficerTrucks as $truck)
                                                    @if($truck->truck_no === $specificRecord->truck_no)
                                                    <option value="{{ $truck->id }}" selected>{{ $truck->truck_no }}</option>
                                                    @else
                                                    <option value="{{ $truck->id }}">{{ $truck->truck_no }}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                                
                                            </td>
                                            <td>{{$specificRecord->loading_site}}</b></span></td>
                                            <td>
                                                <span class="defaultDestination">{{ $specificRecord->exact_location_id }}</span>
                                                <input type="text" name="destinations[]" class="hidden destination" value="{{$specificRecord->exact_location_id}}">
                                            </td>
                                            <td>
                                                @foreach($waybillDetails as $waybill)
                                                    @if($waybill->trip_id == $specificRecord->id)
                                                        {{ $waybill->sales_order_no }} {{ $waybill->invoice_no }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="defaultClientRate">{{ number_format($specificRecord->client_rate, 2) }}</span>
                                                <input type="number" name="clientRates[]" class="hidden clientRate" value="{{$specificRecord->client_rate}}">
                                            </td>
                                            <td>
                                                <span class="defaultTransporterRate">{{ number_format($specificRecord->transporter_rate, 2) }}</span>
                                                <input type="number" name="transporterRates[]" class="hidden transporterRate" value="{{$specificRecord->transporter_rate}}">
                                            </td>
                                            <td>{{ $specificRecord->client_rate - $specificRecord->transporter_rate }}</td>
                                            <td>
                                                <?php
                                                    $difference = $specificRecord->client_rate - $specificRecord->transporter_rate;
                                                    if($specificRecord->client_rate == 0){
                                                        $markUp = 0.00;
                                                    }
                                                    else{
                                                        $markUp = $difference / $specificRecord->client_rate * 100;
                                                    }
                                                ?>
                                                {{ number_format($markUp,2)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="4">No record is available.</td></tr>
                                    @endif
                                    
                                    <input type="hidden" value="{{ number_format($extremeWaybillValuation,2) }}" id="calculatedValuation">
                                </tbody>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>  
    </form>
</div>


