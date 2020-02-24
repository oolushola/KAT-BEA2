<div class="card">
    <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title font-weight-semibold">GATE OUT
            <span class="text-danger-400 font-weight-semibold hidden" style="font-size:11px;" id="gateOutPlaceholder">
                <input type="datetime-local" class="form-control" name="gated_out" id="gatedOut" value="<?php if(isset($recid[0]->gated_out)!='') { echo $recid[0]->gated_out; } else { echo date('Y-m-d\TH:i'); } ?>">

            </span>
        </h6>
        <span></span>
        
        <span style="font-size:8px; font-family:tahoma; cursor:pointer; color:blue; font-weight:bold" id="changeGateOutTime">Change Time and Date of Gate Out</span>

    </div>
    <div class="row ml-3 mr-3 mt-3">
        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-semibold">Customer's Name</label>
                <input type="text" class="form-control" name="customers_name" id="customerName" value="<?php if(isset($recid)) { echo $recid[0]->customers_name; } ?>">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-semibold">Customer's Number</label>
                <input type="text" class="form-control" name="customer_no" id="customerNumber" value="<?php if(isset($recid)) { echo $recid[0]->customer_no; } ?>">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-semibold">Loaded Quantity</label>
                <input type="number" class="form-control" min="1" name="loaded_quantity" id="loadedQuantity" value="<?php if(isset($recid)) { echo $recid[0]->loaded_quantity; } ?>"> 
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-semibold">Loaded Weight<sub>(Kg)</sub></label>
                <input type="text" class="form-control" name="loaded_weight" id="loadedWeight" value="<?php if(isset($recid)) { echo $recid[0]->loaded_weight; } ?>"> 
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="font-weight-semibold">Customer's Address</sub></label>
                <textarea class="form-control" name="customer_address" id="customerAddress"><?php if(isset($recid)) { echo $recid[0]->customer_address; } ?></textarea>
            </div>
        </div>
    </div>
    <div class="text-left  ml-3 mr-3 mb-3">
        @if(isset($recid) && ($recid[0]->tracker) <=4)
        <button type="submit" class="btn btn-primary" id="updateGatedOut">Update Gate Out Information</button>
        @endif
        @if(isset($recid) && ($recid[0]->tracker) >=5)
        <button type="submit" class="btn btn-primary" id="updateGatedOut">Update</button>
        @endif
    </div>
</div>