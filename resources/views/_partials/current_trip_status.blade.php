<!-- Modal HTML for Advance Request -->
<div id="currentTripStatus" class="modal fade" >
        <div class="modal-dialog">
            <div class="modal-content" style="width:700px;">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">Trip status as at {{ date('Y-m-d') }}</h5>
                    <span class="ml-2"></span>
                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                    
                </div>
                
                <div class="modal-body">
                
                   <div class="btn-group" style="margin-left:10px;">
                        <button  id="btn-gate-in" class="btn btn btn-primary" data-target="gate-in-container">Gate In</button>
                        <button id="btn-at-loading-bay" data-target="at-loading-bay-container" class="btn btn btn-primary">At Loading Bay</button>
                        <button id="btn-departed-loading-bay" data-target="departed-loading-bay-container" class="btn btn btn-primary" type="button">Departed Loading Bay</button>
                        <button id="btn-on-journey" data-target="on-journey-container" class="btn btn btn-primary">On Journey</button>
                        <button id="btn-at-destination" data-target="at-destination-container" class="btn btn btn-primary" >At Destination</button>
                        <button id="btn-offloaded" data-target="offloaded-container" class="btn btn btn-primary" >Offloaded</button>
                    </div>

                    <div style="padding:10px;"><input type="text" class="form-control" id="searchDataset" placeholder="SEARCH"></div>
                       
                    
                
                   <div class="table-responsive container" id="gate-in-container">
                        {!! displayRecords('GATE IN', $gateInData, 'gate_in') !!}
                    </div>

                    <div class="table-responsive container" id="at-loading-bay-container">
                        {!! displayRecords('LOADING BAY', $atloadingbayData, 'arrival_at_loading_bay') !!}
                    </div>

                    <div class="table-responsive container" id="departed-loading-bay-container">
                        {!! displayRecords('DEPARTED LOADING BAY', $departedLoadingBayData, 'departure_date_time') !!}
                    </div>

                    <div class="table-responsive container" id="on-journey-container">
                        {!! displayRecords('ON JOURNEY', $onJourneyData, 'gated_out') !!}
                    </div>
                    
                    <div class="table-responsive container" id="at-destination-container">
                        {!! displayRecords('AT DESTINATION', $atDestinationData, 'gated_out') !!}
                    </div>

                    <div class="table-responsive container" id="offloaded-container">
                        {!! displayRecords('OFFLOADED', $offloadedData, 'gated_out') !!}
                    </div>

            

                </div>

            </div>
        </div>  
    </form>
    
    

</div>

<?php 
    
    function displayRecords($activeSubheading, $arrayObject, $fieldLabel) {
        $data = '<table class="table table-striped table-hover">
            <thead>
                <tr class="table-success">
                    <th>SN</th>
                    <th>'.$activeSubheading.'</th>
                    <th width="40%">TRUCK</th>
                    <th>ORDER DETAILS</th>
                </tr>
            </thead>
            <tbody id="masterDataTable">';

                if(count($arrayObject)) {
                    $count = 0;
                    foreach($arrayObject as $object) {
                        $count +=1;
                        $data.='<tr>
                        <td>('.$count.')</td>
                        <td>
                            <p class="font-weight-bold" style="margin:0">'.$object->trip_id.'</p>
                            <p  style="margin:0; "class="text-warning font-weight-bold">'.$object->loading_site.',</p>
                            <p>'.date('d-m-Y', strtotime($object->$fieldLabel)).' <br> '.date('H:i A', strtotime($object->$fieldLabel)).'</p>
                        </td>
                        <td>
                            <span class="text-primary"><b>'.$object->truck_no.'</b></span>
                            <p style="margin:0"><b>Truck Type</b>: '.$object->truck_type.', '.$object->tonnage/1000 .'<sub>t</sub></p>
                            <p style="margin:0"><b>Transporter</b>: '.$object->transporter_name.', '.$object->phone_no.'</p>
                        </td>
                        
                        <td><p style="margin:0" class="text-primary font-weight-bold">Destination</p>
                            <p style="margin-bottom:3px">'.$object->exact_location_id.'</p>
                            <p  style="margin:0" class="text-primary font-weight-bold">Product</p>
                            <p style="margin:0">'.$object->product.'</p>
                        </td>
                        
                    </tr>';
                    }
                }
                else {
                    $data.='<tr><td colspan="3">No record is available.</td></tr>';
                }
                
            $data.='</tbody>
        </table>';
        return $data;
    }
    
?>


