<div class="modal fade expensesBreakdown">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="expensesModelInfo"></h5>
                <input type="hidden" class="expensesModelInfo" />
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body" style="background:#ccc">
                <div id="paymentStatistics"></div>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="card" style="border:none">
                            <div class="card-body text-center">
                                <span class="font-weight-bold d-block"></span>
                                <canvas id="expensesModel" height="120"></canvas>
                            </div>                            
                        </div>
                        <div class="card d-none" id="detailedBreakdown">
                            <span><i class="spinner icon-spinner3"></i>Please wait...</span>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>  
</div>