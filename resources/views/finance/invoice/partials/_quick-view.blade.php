<div class="modal fade quickInvoiceOverview">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="z-index: 1000; position: relative">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold" id="invoiceNoPlaceholder"></h5>
                <button type="button" class="close" data-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="frmUpdatePayment">
                    @csrf
                    <input type="hidden" name="checker" value="1">
                    <div id="invoiceQuickViewController"></div>
                </form>
            </div>
        </div>
    </div>  
</div>
