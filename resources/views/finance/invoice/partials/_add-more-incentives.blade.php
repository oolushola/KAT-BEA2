<!-- Modal HTML for Advance Request -->
<div id="addMoreIncentives" class="modal fade">
  <form method="POST" name="frmAddMoreIncentives" id="frmAddMoreIncentives" >
    @csrf
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="font-weight-sm">
            ADD MORE INCENTIVES ON INVOICE: <span id="invoiceNoHolder"></span>
            <input type="hidden" id="completeInvoiceNo_" name="completeInvoiceNo_" />
          </h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <div class="modal-body" id="addMoreIncentiveInvoice"></div>
        
      </div>
    </div>  
  </form>
</div>
