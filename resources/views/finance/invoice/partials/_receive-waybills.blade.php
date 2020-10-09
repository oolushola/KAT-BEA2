<div class="modal fade receivedWaybillLog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mt-md-2 font-weight-semibold m-0" id="invoiceNoPlaceholder">RECEIVE TRIPS</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <form method="POST" id="bulkReceiveWaybill" />
                    @csrf
                    <div class="table-responsive" id="logOfWaybillsToReceive"></div>
                </form>
            </div>
        </div>
    </div>  
</div>
