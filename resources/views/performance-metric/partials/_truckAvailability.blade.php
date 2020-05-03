<!-- Modal HTML for Advance Request -->
<div id="truckAvailability" class="modal fade" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">TRUCK AVAILABILITY {{ strtoupper(date('F')) }}</h5>
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
                                    <th width="25%">AVAILABLE AT</th>
                                    <th>TRUCK DETAILS</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyGatedOutData">
                                <?php $count = 1; ?>
                                @if(count($availableTrucks))
                                    @foreach($availableTrucks as $availableTruck)
                                    <tr>
                                        <td>({{ $count++ }})</td>
                                        <td>
                                            
                                            <p class="font-weight-bold" style="margin:0; padding:0">{{$availableTruck->loading_site}}</p>
                                            <p style="margin:0"><span class="text-primary font-weight-bold">Location</span>: {{ $availableTruck->exact_location_id }}</p>
                                            <p style="margin:0"><span class="text-primary font-weight-bold">Product:</span> {{ $availableTruck->product }}</p>
                                            
                                        </td>
                                        <td>
                                            <span class="text-primary"><b>{{$availableTruck->truck_no}}</b></span>
                                            <p style="margin:0"><b>Truck Type</b>: {{ $availableTruck->truck_type }} {{ $availableTruck->tonnage / 1000 }}T</p>
                                            <p style="margin:0"><b>{{$availableTruck->transporter_name}}</b>: {{ $availableTruck->phone_no }}</p>
                                        </td>
                                        
                                        
                                        <td>
                                            <p style="margin:0" class="text-primary-400 font-weight-bold">Status: {{ $availableTruck->truck_status }}</p>
                                            <p class="font-size-sm">Profiled by: {{ucfirst($availableTruck->first_name)}} {{ ucfirst($availableTruck->last_name)}}, at {{date('Y-m-d H:i A', strtotime($availableTruck->updated_at))}}</p>
                                            

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


