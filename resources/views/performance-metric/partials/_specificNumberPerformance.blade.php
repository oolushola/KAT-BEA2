<div id="specificBuh" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header" style="padding:5px; background:#324148">
                <h5 class="font-weight-sm font-weight-bold text-warning" id="loaderSpecific">&nbsp;</h5>
                <span class="ml-2"></span>
                <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
            </div>
            
            <div class="modal-body d-none" id="modalBody">
                
                <div class="row">
                    <div class="col-md-6 col-sm-12 mb-2">
                        <div style="max-height:350px; overflow:auto;">
                            <div class="dashboardbox" id="selectedMonthAndData"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mt-2">
                                <div class="dashboardbox" style="">
                                    <canvas id="targetDoughnut" height="250"></canvas>
                        
                                    <p class="font-weight-bold" style="display:inline-block">
                                        GTV: ₦<span id="gtvText"></span>
                                    </p>
                                    <p class="font-weight-bold" style="display:inline-block; float:right">
                                        TR: ₦<span id="trText"></span>
                                    </p>

                                    <p class="text-center text-danger font-weight-bold">
                                        <span class="text-primary" id="targetPercentage">0.00</span>% of ₦<span id="targetAmount"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-12 mt-2" id="averageRatingsChart">
                                
                            </div>
                            <div class="col-md-3 col-sm-12 mt-2">
                                <canvas id="selectedMonthMarkup" height="700"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-2">
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="dashboardbox p-0">
                                    <p class="font-weight-bold text-center">Their daily number of gate out count</p>
                                    <canvas id="dailyGateOutChart" height="150"></canvas>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 mb-2">
                                <div style="max-height:400px; overflow:auto;">
                                    <div class="dashboardbox" id="yetToGateOutRecord"></div>
                                </div>
                            </div>

                        </div>
                        
                    </div>
                </div>

            </div>

        </div>
    </div>  
</div>














