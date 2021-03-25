<!-- Modal Html for payment voucher approval -->
<div class="modal fade approvePaymentVoucher">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="selectedClient">
                    Payment Voucher Approvals
                    <input type="checkbox" id="approveAllPaymentVouchers">
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding-top:0px; margin-top:0px;">
                <form method="POST" name="frmApprovePaymentVoucher" id="frmApprovePaymentVoucher">
                    @csrf {!! method_field('PATCH') !!}
                    <div id="voucherApprovalListings"></div>
                </form>
            </div>
        </div>
    </div>  
</div>