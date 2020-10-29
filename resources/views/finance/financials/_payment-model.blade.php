<div class="modal fade paymentModel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="selectedClient"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body" style="background:#ccc">
                <div id="paymentStatistics"></div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="card" style="border:none">
                            <div class="card-body text-center">
                                <span class="font-weight-bold d-block">Payment trajectory of last 20 Invoices</span>
                                <canvas id="paymentTrajectory" height="80"></canvas>
                            </div>                            
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>  
</div>