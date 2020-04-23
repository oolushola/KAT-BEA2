<!-- Modal HTML for Advance Request -->
<div id="waybillReportStatus" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content" style="width:1200px; position:relative; left:-300px">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">Waybill Status as at {{ date('d-m-Y') }}</h5>
                    <span class="ml-2"></span>

                    <!-- <input type="text" class="" id="searchWaybillStatus" placeholder="SEARCH"> -->

                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                    
                </div>
                
                <div class="modal-body">
                   <div class="row waybillStatus">
                        <table class="table table-condensed">
                            <thead class="table-success">
                                <!-- <tr>
                                    <th colspan="7"></th>
                                    <th class="bg-primary">VALUED AT: <span id="extremeValuation"></span></th>
                                    <th colspan="3"></th>
                                </tr> -->
                                <tr class="text-center bg-success" style="font-size:10px;" >
                                    <td>SN</td>
                                    <td class="text-center font-weight-bold">KAID</td>
                                    <td class="font-weight-bold text-center">WAYBILL DETAILS</td>
                                    <td class="text-center">TRUCK NO.</td>
                                    <td>DESTINATION</td>
                                    <td>LOADED AT</td>
                                    <td>PRODUCT</td>
                                    <!-- <td class="text-center">RATE</td> -->
                                    <td>WAYBILL STATUS</td>
                                    <td class="text-center">NO OF DAYS</td>
                                    <td>TRANSPORTER</td>
                                </tr>
                                <tbody id="currentGateOutData">
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
                                            }
                                            elseif($numberofdays >=4 && $numberofdays <= 7){
                                                $bgcolor = '#FFBF00';
                                                $color = '#000';
                                            }
                                            else{
                                                $bgcolor = '#FF0000';
                                                $extremeWaybillValuation += $specificRecord->client_rate;
                                                $color = '#000';
                                            }
                                        ?>
                                        <tr style="font-size:11px; color:{{ $color }}; background-color:{{ $bgcolor }}" class="text-center">
                                            <td>({{ $count }})</td>
                                            <td style="padding:0;" class="text-center">{{ $specificRecord->trip_id }}</td>
                                            <td class="text-center">
                                                @foreach($tripWaybills as $tripWaybill)
                                                    @if($specificRecord->id == $tripWaybill->trip_id)
                                                    <span class="mb-2 d-block">{{$tripWaybill->sales_order_no}} {{$tripWaybill->invoice_no}}</span>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="text-center">{{$specificRecord->truck_no}}</b></span></td>
                                            <td>{{$specificRecord->exact_location_id}}</td>

                                            <td>{{ $specificRecord->loading_site }}</td>
                                            <td>{{ $specificRecord->product }}</td>
                                            <!-- <td>{{ number_format($specificRecord->client_rate, 2) }}</td> -->
                                            <td>{{ $specificRecord->comment }}</td>
                                            <td>@if($numberofdays <= 0) < A Day @elseif($numberofdays == 1) 1 Day @else {{$numberofdays}} Days @endif</td>
                                            <td>{{ $specificRecord->transporter_name }}</td>
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


