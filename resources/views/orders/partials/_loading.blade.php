@if(isset($recid) && ($recid[0]->tracker)>=2 && ($recid[0]->loading_start_time == '' || $recid[0]->loading_end_time == ''))
    <div class="card">
    @elseif(isset($recid) && ($recid[0]->tracker)>=2 && ($recid[0]->loading_start_time != '' || $recid[0]->loading_end_time != ''))
    <div class="card">
    @else
    <div class="card hidden" id="loadingContainer">
    @endif
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title font-weight-semibold">Loading Information</h6>
        </div>
        <div class="row">
            <div class="col-md-4 ml-3 mr-3 mt-3">
                <div class="form-group">
                    <label class="font-weight-semibold">Time Loading Start</label>
                    <input type="datetime-local" class="form-control" name="loading_start_time" id="timeLoadingStart" value="<?php if(isset($recid[0]->loading_start_time) !='') { echo $recid[0]->loading_start_time; } else { echo date('Y-m-d\TH:i'); } ?>">
                </div>
            </div>
            <div class="col-md-4  ml-3 mr-3 mt-3">
                <div class="form-group">
                    <label class="font-weight-semibold">Time Loading Ends</label>
                    <input type="datetime-local" class="form-control" name="loading_end_time" id="timeLoadingEnd" value="<?php if(isset($recid[0]->loading_end_time) != '') { echo $recid[0]->loading_end_time; } else { echo date('Y-m-d\TH:i'); } ?>" @if(isset($recid[0]->loading_end_time) != "") @endif>
                </div>
            </div>
        </div>
        <div class="text-left  ml-3 mr-3 mb-3">
                @if(isset($recid) && ($recid[0]->tracker) <=2 )
                    <button type="submit" class="btn btn-primary" id="updateLoadingInformation">Update Loading Information</button>
                @endif
        </div>
    </div>