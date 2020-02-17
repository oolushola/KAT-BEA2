<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title font-weight-semibold">LOADING BAY
            <span class="text-danger-400 font-weight-semibold hidden" style="font-size:11px;" id="ArrivalPlaceholder">
                <input type="datetime-local" class="form-control" name="arrival_at_loading_bay" id="loadingBayArrival" value="<?php if(isset($recid) && $recid[0]->arrival_at_loading_bay != '' ) { echo $recid[0]->arrival_at_loading_bay; } else { echo date('Y-m-d\TH:i'); } ?>" >
            </span>
        </h6>

        <span></span>
        
        <span style="font-size:8px; font-family:tahoma; cursor:pointer; color:blue; font-weight:bold" id="changeArrivalTime">Change Time and Date of Loading Bay Arrival</span>

    </div>


    <!-- <div class="row">
        <div class="col-md-4 ml-3 mr-3 mt-3">
            <div class="form-group">
                <label class="font-weight-semibold">Date & Time</label>
                <input type="datetime-local" class="form-control" name="arrival_at_loading_bay" id="loadingBayArrival" value="<?php //if(isset($recid[0]->arrival_at_loading_bay) !='') { echo $recid[0]->arrival_at_loading_bay; } ?>" >
            </div>
        </div>
    </div> -->
    <div class="text-left  ml-3 mr-3 mb-3">
        @if(isset($recid) && ($recid[0]->tracker) <=1 )
        <button type="submit" class="btn btn-primary mt-2" name="updateArrivalAtloadingBay" id="updateArrivalAtloadingBay">
            Update Arrival at Loading Bay</button>
        @endif
    </div>
</div>
