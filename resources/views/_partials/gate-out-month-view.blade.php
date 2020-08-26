<!-- Modal HTML for Advance Request -->
<div id="totalGateOutForTheMonth" class="modal fade" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">GATED OUT FOR {{ strtoupper(date('F')) }}</h5>
                    <span class="ml-2"></span>
                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                    
                </div>
                <div class="modal-body">
                    <div style="padding:10px;"><input type="text" class="form-control" id="searchGatedOut" placeholder="SEARCH"></div>
                    <div class="row" id="currentMonthGateOutTripListings"></div>
                </div>
            </div>
        </div>  
    </form>
</div>


