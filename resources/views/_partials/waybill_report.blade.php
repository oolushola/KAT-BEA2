<!-- Modal HTML for Advance Request -->
<div id="waybillReportStatus" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header" style="padding:5px; background:#324148">
                <h5 class="font-weight-sm font-weight-bold text-warning">Waybill Status as at {{ date('d-m-Y') }}</h5>
                <span class="ml-2"></span>

                <!-- <input type="text" class="" id="searchWaybillStatus" placeholder="SEARCH"> -->

                <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                
            </div>
            <div class="mt-2">
                <a class="m-0 font-weight-semibold pointer mt-3" id="exportWaybillStatus"><span class="icon-download ml-3"></span> Export to Excel</a> 
                <select id="searchWaybillReportData" class="ml-1" style="border:1px solid #ccc; padding:4px; ">
                    <option value="0">Filter</option>
                    <option value="On Journey">On Journey</option>
                    <option value="Arrived Destination">Arrived Destination</option>
                    <option value="Offloaded">Offloaded</option>
                </select>
            </div>
                @if(Auth::user()->role_id != 4 && Auth::user()->email != 'odejobi.olushola@kayaafrica.co') 
                    <?php $waybillVisibility = 'd-none'; ?>
                @else
                    <?php $waybillVisibility = ''; ?>
                @endif
            <div class="modal-body">
                <div class="row waybillStatus table-responsive">
                    <form method="POST" id="updateAllReceivedWaybills">
                        @csrf 
                    <table class="table table-condensed" id="exportTableData">
                        <thead class="table-success">
                            <tr class="{{$waybillVisibility}}">
                                <th colspan="15">
                                    <button id="receiveAllWaybills" class="btn btn-success btn-sm font-weight-semibold">RECEIVE ALL</button>
                                    <span id="waybillAction"></span>
                                </th>
                            </tr>
                            <tr class=" bg-success" style="font-size:10px;" >
                                <td class="text-center {{$waybillVisibility}}">
                                    <input type="checkbox" id="checkAllWaybills">
                                </td>
                                <td class="serialNumber">SN</td>
                                <td class="font-weight-bold">KAID</td>
                                <td class="font-weight-bold text-center">INVOICE NO</td>
                                <td class="font-weight-bold text-center">SO NUMBER</td>
                                <td class="text-center">TRUCK NO.</td>
                                <td>DESTINATION</td>
                                <td>LOADED AT</td>
                                <td>PRODUCT</td>
                                <!-- <td class="text-center">RATE</td> -->
                                <td>WAYBILL STATUS</td>
                                <td class="text-center">NO OF DAYS</td>
                                <td>TRANSPORTER</td>
                            </tr>
                            </thead>
                            <tbody id="currentGateOutDataForWaybillReport">
                                <?php $count = 0; $extremeWaybillValuation = 0; ?>
                                @if(count($tripWaybillYetToReceive))
                                    @foreach($tripWaybillYetToReceive as $specificRecord)
                                    <?php 
                                        $count++;
                                        $now = time();
                                        $gatedOut = strtotime($specificRecord->gated_out);;
                                        $datediff = $gatedOut - $now;
                                        $numberofdays = (floor($datediff / (60 * 60 * 24)) * -1) -1;

                                        if($numberofdays >=0 && $numberofdays <= 3){
                                            $bgcolor = '#008000';
                                            $color = '#fff';
                                            $checker = '';
                                        }
                                        elseif($numberofdays >=4 && $numberofdays <= 7){
                                            $bgcolor = '#FFBF00';
                                            $color = '#000';
                                            $checker = '';
                                        }
                                        elseif($numberofdays > 7 && $specificRecord->tracker != 8) {
                                            $bgcolor = '#FFBF00';
                                            $color = '#000';
                                            $checker = '';
                                        }
                                        else{
                                            if($numberofdays > 7 && $specificRecord->tracker == 8) {
                                                $bgcolor = '#FF0000';
                                                $extremeWaybillValuation += $specificRecord->client_rate;
                                                $color = '#000';
                                                $checker = '';
                                            }
                                        }
                                    ?>
                                    <tr style="font-size:11px; color:{{ $color }}; background-color:{{ $bgcolor }}; cursor:pointer">
                                        <td class="text-center {{$waybillVisibility}}">
                                            <input type="checkbox" class="waybillStatusChecker" name="tripIds[]" value="{{$specificRecord->id}}" />
                                        </td>
                                        <td class="serialNumber">
                                            ({{ $count }})
                                        </td>
                                        <td>{{ $specificRecord->trip_id }}</td>
                                        <td style="padding:0;" class="text-center">
                                            @foreach($tripWaybills as $tripWaybill)
                                                @if($specificRecord->id == $tripWaybill->trip_id)
                                                <span class="mb-2 d-block">{{ strtoupper($tripWaybill->invoice_no) }}</span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            @foreach($tripWaybills as $tripWaybill)
                                                @if($specificRecord->id == $tripWaybill->trip_id)
                                                <span class="mb-2 d-block">{{strtoupper($tripWaybill->sales_order_no)}}</span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td class="text-center">    </b></span></td>
                                        <td>{{ ucwords($specificRecord->exact_location_id) }}</td>

                                        <td>{{ $specificRecord->loading_site }}</td>
                                        <td>{{ $specificRecord->product }}</td>
                                        <!-- <td>{{ number_format($specificRecord->client_rate, 2) }}</td> -->
                                        <td>{{ ucfirst($specificRecord->comment) }}</td>
                                        <td>@if($numberofdays <= 0) < A Day @elseif($numberofdays == 1) 1 Day @else {{$numberofdays}} Days @endif</td>
                                        <td>{{ ucwords($specificRecord->transporter_name) }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="4">No record is available.</td></tr>
                                @endif
                                
                                <input type="hidden" value="{{ number_format($extremeWaybillValuation,2) }}" id="calculatedValuation">
                            </tbody>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>  
</div>


