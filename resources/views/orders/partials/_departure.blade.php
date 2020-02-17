<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title font-weight-semibold">DEPARTURE</h6>
    </div>

    <div class="row">
        <div class="col-md-4 ml-3 mr-3 mt-3">
            <div class="form-group">
                <label class="font-weight-semibold">Loading bay departure time & date</label>
                <input type="datetime-local" class="form-control" name="departure_date_time" id="departure" value="<?php if(isset($recid[0]->departure_date_time)!='') { echo $recid[0]->departure_date_time; } else { echo date('Y-m-d\TH:i'); } ?>">
            </div>
        </div>
    </div>
    <div class="text-left  ml-3 mr-3 mb-3 mt-2">
        @if(isset($recid) && ($recid[0]->tracker) <=3 )
        <button type="submit" class="btn btn-primary" id="updateDeparture">Update Loading bay Departure</button>
        @endif
    </div>
</div>