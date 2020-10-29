<div class="modal fade quickTripFinder">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="selectedDatePlaceHolder"></h5>
                @if(Auth::user()->email != 'timi@kayaafrica.co')
                <input type="text" class="d-inline t-2 font-weight-semibold findTrip" style="width:80px; border:1px solid #ccc; padding:5px; top:3px; position:relative" placeholder="TRIP ID" id="finderRangeFrom"  />
                <input type="text" class="d-inline t-2 font-weight-semibold findTrip" style="width:80px; border:1px solid #ccc; padding:5px; top:3px; position:relative" placeholder="TRIP ID" id="finderRangeTo"  />
                @endif

                <span id="searchByWaybill" style="" class="pointer text-info font-size-xs font-weight-semibold mt-2 ml-1">Click here to find by waybill no.</span>
                <span id="SearchByOthers" style="" class="pointer text-primary font-size-xs font-weight-semibold d-none mt-2 ml-1">Waybill No</span>

                <input type="text" class="d-inline ml-2 t-2 font-weight-semibold" style="width:180px; border:1px solid #ccc; padding:5px; top:3px; position:relative" placeholder="What are you looking for?" id="searchTripFinder" data-value="1" />
                

                <span id="finderLoader" class="mt-2 ml-2"></span>

                @if(Auth::user()->email != 'timi@kayaafrica.co')
                <span class="icon-cloud-download2 mt-2 ml-2 pointer" id="quickTripDownload"></span>
                @endif

                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive"  id="finderResult">
                    <table class="table table-bordered">
                        <thead class="table-success" style="font-size:11px">
                            <tr>
                                <th>KAID</th>
                                <th>LOADING SITE</th>
                                <th>TRUCK NO</th>
                                <th>TRANSPORTER</th>
                                <th>DESTINATION</th>
                                <th>GATE OUT</th>
                                <th>CURRENT STAGE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7">You haven't entered any trip id</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>  
</div>