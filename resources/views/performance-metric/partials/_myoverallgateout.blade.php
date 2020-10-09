<!-- Modal HTML for Advance Request -->
<div id="overAllGateTrips" class="modal fade">
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
                                <tr>
                                    <td colspan="7">&nbsp;</td>
                                    <td class="bg-primary font-weight-bold" id="clientRateholder"></td>
                                    <td class="bg-primary font-weight-bold" id="transporterRateHolder"></td>
                                    <td class="bg-primary font-weight-bold" id="grossMarginHolder"></td>
                                    <td class="bg-primary font-weight-bold" id="averageMarginHolder"></td>
                                </tr>
                                <tr class="font-weight-bold bg-success" style="font-size:10px;" >
                                    <td>SN</td>
                                    <td class="text-center">KAID</td>
                                    <td class="text-center" width="10%">TRUCK NO.</td>
                                    <td>LOADING SITE</td>
                                    <td>DESTINATION</td>
                                    <td>CUSTOMER</td>
                                    <td>PRODUCT</td>
                                    <td>CLIENT RATE</td>
                                    <td>TRANSPORTER RATE</td>
                                    <td>MARGIN</td>
                                    <td>MARKUP(%)</td>
                                </tr>
                                <tbody id="currentGateOutData">
                                    <?php $count = 0; $sumOfClientRate = 0; $sumOfTransporterRate = 0; $sumOfAverageMargin = 0; $sumOfGrossMargin = 0; ?>
                                    @if(count($totalTripsData))
                                        @foreach($totalTripsData as $specificRecord)
                                        <tr style="font-size:11px;" class="font-weight-semibold">
                                            <td>({{ $count+=1 }})</td>
                                            <td style="padding:0;" class="text-center">{{ $specificRecord->trip_id }}</td>
                                            <td class="text-center">{{ $specificRecord->truck_no }}</td>
                                            <td>{{$specificRecord->loading_site}}</b></span></td>
                                            <td>{{$specificRecord->exact_location_id}}</td>

                                            <td>{{ $specificRecord->customers_name }}</td>
                                            <td>{{ $specificRecord->product }}</td>
                                            <td>{{ number_format($specificRecord->client_rate, 2) }}</td>
                                            <td>{{ number_format($specificRecord->transporter_rate, 2) }}</td>
                                            <td>{{ number_format($specificRecord->client_rate - $specificRecord->transporter_rate, 2) }}</td>
                                            <td>
                                                <?php
                                                    $difference = $specificRecord->client_rate - $specificRecord->transporter_rate;
                                                    if($specificRecord->client_rate == 0){
                                                        $markUp = 0.00;
                                                    }
                                                    else{
                                                        $markUp = $difference / $specificRecord->client_rate * 100;
                                                    }
                                                    $sumOfClientRate += $specificRecord->client_rate;
                                                    $sumOfTransporterRate += $specificRecord->transporter_rate;
                                                    $sumOfGrossMargin += $difference;
                                                    
                                                ?>
                                                {{ number_format($markUp,2)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <input type="hidden" value="{{number_format($sumOfClientRate, 2)}}" id="totalClientRate">
                                            <input type="hidden" value="{{number_format($sumOfTransporterRate, 2)}}" id="totalTransporterRate">
                                            <input type="hidden" value="{{number_format($sumOfGrossMargin, 2)}}" id="totalGrossMargin">
                                            <input type="hidden" value="{{number_format(($sumOfClientRate - $sumOfTransporterRate) / $sumOfClientRate * 100, 2)}}" id="averageMargin">
                                            
                                        </tr>
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



