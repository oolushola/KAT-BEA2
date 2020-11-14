<!-- Modal HTML for Advance Request -->
<div id="currentTripStatus" class="modal fade" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding:5px; background:#324148">
                <h5 class="font-weight-sm font-weight-bold text-warning">Trip status: <span id="selectedStatus">NON SELECTED</span></h5>
                <span class="ml-2"></span>
                <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
            </div>
            
            <div class="modal-body">
                <ul>
                    <li class="tripStatusMenu" id="tsGateIn">Gate In</li>
                    <li class="tripStatusMenu" id="tsAtLoadingBay">At Loading Bay</li>
                    <li class="tripStatusMenu" id="tsDepartedLoadingBay">Departed Loading Bay</li>
                    <li class="tripStatusMenu" id="tsOnJourney">On Journey</li>
                    <li class="tripStatusMenu" id="tsAtDestination">At Destination</li>
                    <li class="tripStatusMenu" id="tsOffloaded">Offloaded</li>
                </ul>
                <div style="padding:10px;"><input type="text" class="form-control" id="searchDataset" placeholder="SEARCH"></div>
                <div class="table-responsive container" id="tripStatusResponse"></div>
            </div>
        </div>
    </div>  
</div>


