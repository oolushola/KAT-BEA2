<!-- Modal Html for Beneficiary -->
<div class="modal fade beneficiary">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="selectedClient">Beneficiary</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="frmBeneficiary">
              @csrf
              <div class="modal-body">
                  <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="first name">First Name</label>
                          <input type="text" class="form-control" name="first_name" id="firstName">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="last name">Last Name</label>
                          <input type="text" class="form-control" name="last_name" id="lastName" value="">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label for="last name">Bank Name</label>
                          <input type="text" class="form-control" name="bank_name" id="bankName" value="">
                        </div>
                      </div>
                      <div class="col-md-8">
                        <div class="form-group">
                          <label for="last name">Account Name <input type="checkbox" class="useFirstAndLastName ml-3"><span style="color: #000">&nbsp; Same as first and last name</span></label>
                          <input type="text" class="form-control" name="account_name" id="accountName" value="">
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="last name">Account No.</label>
                          <input type="number" class="form-control" style="font-size:20px; letter-spacing:20px" name="account_no" id="accountNo" value="">
                        </div>
                      </div>
                      <div class="col-md-12 mt-1">
                          <button class="btn-danger btn-sm font-weight-semibold addBeneficiary">Add New Beneficiary</button>
                      </div>
                  </div>
                <div id="dataDropper" class="mt-3"></div>
              </div>
            </form>
        </div>
    </div>  
</div>