<!-- Modal HTML for Advance Request -->
<div id="addAgo" class="modal fade">
  <form method="POST" id="frmReverseAgo">
    @csrf
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="font-weight-sm">
            ADD A.G.O <span id="agoInvoiceNoHolder"></span>
            <input type="hidden" id="agoInvoiceNo" name="agoInvoiceNo" />
          </h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <div class="modal-body" id="addAgoForm"></div>
        
      </div>
    </div>  
  </form>
</div>
