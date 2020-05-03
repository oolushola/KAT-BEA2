<!-- Modal HTML for Advance Request -->
<div id="yetToGateOut" class="modal fade" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">TRUCK WITHIN PREMISE </h5>
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
                                    <th>KAID</th>
                                    <th>DESTINATION</th>
                                    <th>TRUCK DETAILS</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyGatedOutData">
                                <?php $count = 1; ?>
                                @if(count($yetTogateOut))
                                    @foreach($yetTogateOut as $atPremise)
                                    <tr>
                                        <td>({{ $count++ }})</td>
                                        <td class="font-weight-semibold">{{ $atPremise->trip_id }}</td>
                                        <td>
                                            <p class="font-weight-bold" style="margin:0; padding:0">{{$atPremise->loading_site}}</p>
                                            <p style="margin:0"><span class="text-primary font-weight-bold">Location</span>: {{ $atPremise->exact_location_id }}</p>
                                            <p style="margin:0"><span class="text-primary font-weight-bold">Product:</span> {{ $atPremise->product }}</p>
                                            
                                        </td>
                                        <td>
                                            <span class="text-primary"><b>{{$atPremise->truck_no}}</b></span>
                                            <p style="margin:0"><b>Truck Type</b>: {{ $atPremise->truck_type }} {{ $atPremise->tonnage / 1000 }}T</p>
                                            <p style="margin:0"><b>{{$atPremise->transporter_name}}</b>: {{ $atPremise->phone_no }}</p>
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


