
  <div id="newRateSheet" class="modal fade" >
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" style="padding:5px; background:#324148">
          <h5 class="font-weight-sm font-weight-bold text-warning">
            <span id="selectedStatus">Rate Sheet Form</span>
          </h5>
          <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
        </div>
          
        <div class="modal-body"> 
          <div class="row">
            <div class="col-md-12">
              &nbsp;
              <form id="frmBulkRateSheet" method="POST" action="{{URL('bulk-rate-sheet')}}">
                @csrf
                <p id="showBulkRate" class="pointer font-weight-bold mb-3">Toggle Bulk Rate Upload <input type="hidden" value="0" id="rateSheetToggler" /></p>
                <span class="font-size-sm"><i class="icon-spinner3 spinner d-none" id="updateSpinner"></i></span>
                <div id="bulkUploadForm" class="d-none">
                  <div class="form-group">
                    <label>Upload File</label>
                    <input type="file" name="bulkRateSheet" title="Upload only CSV File"  />
                  </div>
                  <span id="loader1"></span>
                  <button type="submit" class="btn btn-primary" id="uploadBulkRateSheet">Upload Bulk Rates 
                    <i class="icon-paperplane ml-2"></i>
                  </button>
                </div>
              </form>

              <form id="frmRateSheet" method="POST">
                @csrf
                
                  <input type="hidden" name="id" id="id" />
                  
                
                <div id="singleEntryForm">
                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>Client</label>
                      <select class="form-control" name="client_id" id="client">
                        <option value="">Choose Client</option>
                        @foreach($clients as $client)
                        <option value="{{$client->id}}">{{ strtoupper($client->company_name) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label>State of Ax.</label>
                      <select name="state" class="form-control" id="state" >
                        <option value="">State</option>
                        @foreach($states as $state)
                        <option value="{{$state->regional_state_id}}">{{ $state->state }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label>Exact Destination</label>
                      <input type="text" name="exact_location" id="exactDestination" class="form-control" value="">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Tonnage(kg)</label>
                      <input type="number" class="form-control" value="" id="tonnage" name="tonnage">
                    </div>
                    <div class="form-group col-md-6">
                      <label>Client Rate</label>
                      <input type="number" class="form-control" id="clientRate" name="client_rate" value="" style="font-size: 18px" >
                    </div>
                    <div class="form-group col-md-6">
                      <label>Transporter Rate</label>
                      <input type="number" class="form-control" value="" id="transporterRate" name="transporter_rate" style="font-size: 18px" >
                    </div>
                    
                  </div>
                  <div class="text-right">
                    <span id="loader"></span>
                        <button type="submit" class="btn btn-primary d-none" id="updateRateSheet">Update Rate
                        <button type="submit" class="btn btn-primary" id="addRateSheet">Add to Rate Sheet <i class="icon-paperplane ml-2"></i>
                    </button>
                  </div>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>  
    </div>
</div>
</form>


