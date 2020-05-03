<!-- Modal HTML for Advance Request -->
<div id="myCurrentMonthGateOut" class="modal fade">
    <form method="POST" id="frmUpdateClientRate">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content" style="width:1100px; position:relative; left:-200px">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">Total Gate Out: as at {{ date('d-m-Y') }}</h5>
                    <span class="ml-2"></span>

                    <!-- <input type="text" class="" id="searchWaybillStatus" placeholder="SEARCH"> -->

                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                    
                </div>
                
                <div class="modal-body">
                    <div class="row waybillStatus">
                        <table class="table table-condensed">
                            <thead class="table-success">
                                <tr class="font-weight-bold bg-success" style="font-size:10px;" >
                                    <td>SN</td>
                                    <td class="text-center">KAID</td>
                                    <td class="text-center" width="10%">TRUCK NO.</td>
                                    <td>LOADING SITE</td>
                                    <td>DESTINATION</td>
                                    <td>CUSTOMER</td>
                                    <td>PRODUCT</td>
                                    <td>
                                        <span id="clientRateTitle">CLIENT RATE</span>
                                        <button class="btn btn-primary hidden" id="submitClientRate">Save Changes</button> 
                                        <i class="icon-lock5" title="Change Title" style="cursor:pointer" id="unlockClientRate"></i>
                                        <i class="icon-unlocked2 hidden ml-1" title="Change Title" style="cursor:pointer" id="lockClientRate"></i>
                                        
                                    </td>
                                    <td>TRANSPORTER RATE</td>
                                    <td>MARGIN</td>
                                    <td>MARKUP(%)</td>
                                </tr>
                                <tbody id="currentGateOutData">
                                    <?php $count = 0; $extremeWaybillValuation = 0; ?>
                                    @if(count($currentMonthData))
                                        @foreach($currentMonthData as $specificRecord)
                                        <tr style="font-size:11px;" class="font-weight-semibold">
                                            <td>({{ $count+=1 }})</td>
                                            <td style="padding:0;" class="text-center">{{ $specificRecord->trip_id }}</td>
                                            <td class="text-center">{{ $specificRecord->truck_no }}</td>
                                            <td>{{$specificRecord->loading_site}}</b></span></td>
                                            <td>{{$specificRecord->exact_location_id}}</td>

                                            <td>{{ $specificRecord->customers_name }}</td>
                                            <td>{{ $specificRecord->product }}</td>
                                            <td>
                                                <span class="defaultClientRate">{{ number_format($specificRecord->client_rate, 2) }}</span>
                                                <input type="text" class="hidden" value="{{$specificRecord->trip_id}}" name="tripListings[]">
                                                <input type="number" name="clientRates[]" class="hidden clientRate" value="{{$specificRecord->client_rate}}">
                                            </td>
                                            <td>{{ number_format($specificRecord->transporter_rate, 2) }}</td>
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


