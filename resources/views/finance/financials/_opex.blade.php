<!-- Modal Html for Opex -->
<div class="modal fade opex">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="selectedClient">Operating Expenses</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="Incentive">Current Year</label>
                        <select class="form-control" name="opex_year" id="opexYear">
                            <?php
                                for($year = date('Y'); $year >= 2019; $year--) { 
                                    echo  '<option value="'.$year.'">'.$year.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="Incentive">Current Month</label>
                        <select class="form-control" name="opexMonth" id="opexMonth">
                            <?php
                                for($month = 1; $month <= 12; $month++) {
                                    if(isset(($recid)) && ($recid->month == $month)) {
                                        echo '<option value="'.$month.'" selected>'.date('F', mktime(0,0,0,$month, 1, date('Y'))).'</option>';
                                    }
                                    else {
                                    echo '<option value="'.$month.'">'.date('F', mktime(0,0,0,$month, 1, date('Y'))).'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                    <label for="Incentive">Value</label>
                        <input type="number" class="form-control" name="opex_value" id="opexValue" value="10200000" step="0.1">
                    </div>
                    <div class="col-md-12 mt-1">
                        <button class="btn-danger btn-sm font-weight-semibold addOpexValue">Add Opex</button>
                    </div>
                </div>
               <div id="dataDropper"></div>
            </div>
        </div>
    </div>  
</div>