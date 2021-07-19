<!-- Modal Html for transport team report generator -->
<div class="modal fade transportTeamGenerator">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="selectedAccountOfficer">
                  Score Card of Transport Team Between
                  <span class="reportStartDate">
                    <input type="date" id="report_start_from" value="{{ date('Y-m-d', strtotime('last sunday')) }}" style="width: 150px; border: 1px solid #ccc; font-size: 11px; padding: 6px; font-weight: bold" />
                  </span>
                  <span class="reportStartDate">
                    <input type="date" id="report_end_to" value="{{ date('Y-m-d') }}" style="width: 150px; border: 1px solid #ccc; font-size: 11px; padding: 6px; font-weight: bold" />
                  </span>
                  <button class="generateTransportTeamReport btn btn-success btn-sm font-weight-semibold mt-1">SHOOT <i class="icon-paperplane"></i></button>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-4">
                  <h3 class="text-info-400 font-weight-bold">Total Revenue:
                    <span id="grandTotalRevenue"></span>
                  <h3>
                </div>
                <div class="col-md-4">
                  <h3 class="text-danger-600 font-weight-bold">
                    Total Cost: 
                    <span id="grandTotalCost"></span>
                  </h3>
                </div>
                  
                <div class="col-md-4">
                  <h3 class="text-success font-weight-bold">Total Net Margin:
                    <span id="grandTotalNetMargin"></span>
                  </h3>
                </div>
              </div>
              <div id="transportTeamReportCard"></div>
            </div>
        </div>
    </div>  
</div>