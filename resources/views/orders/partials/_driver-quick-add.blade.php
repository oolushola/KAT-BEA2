<div id="driverQuickAdd" class="modal fade">
    @csrf
    <form method="POST" name="frmDriver" id="frmDriver">
        @csrf
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QUICK ADD - DRIVER<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="row ml-3 mr-3 mt-3 show">
                        
                        <input type="text" class="form-control" placeholder="DRIVERS LICENCE NO." name="licence_no" id="licence_no">
                        
                        <div class="col-md-6">
                            <legend class="font-weight-semibold"><i class="icon-steering-wheel mr-2"></i> Driver's Information</legend>

                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" placeholder="John" name="driver_first_name" id="driversFirstName" value="<?php if(isset($recid)) { echo $recid->driver_first_name; } ?>" >
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" placeholder="Doe" name="driver_last_name" id="driversLastName" value="<?php if(isset($recid)) { echo $recid->driver_last_name; } ?>" >
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" class="form-control" placeholder="+234-***-***-****" name="driver_phone_number" id="driversPhoneNumber" value="<?php if(isset($recid)) { echo $recid->driver_phone_number; } ?>">
                            </div>

                        </div>
                        
                        <div class="col-md-6">
                        
                            <legend class="font-weight-semibold"><i class="icon-people mr-2"></i> Motor Boy Information</legend>

                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" placeholder="John" name="motor_boy_first_name" id="motorBoyFirstName" value="<?php if(isset($recid)) { echo $recid->motor_boy_first_name; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" placeholder="Doe" name="motor_boy_last_name" id="motorBoyLastName" value="<?php if(isset($recid)) { echo $recid->motor_boy_last_name; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" class="form-control" placeholder="+234-***-***-****" name="motor_boy_phone_no" id="motorBoyPhoneNumber" value="<?php if(isset($recid)) { echo $recid->motor_boy_phone_no; } ?>">
                            </div>
                        
                        </div>
                        <div class="text-left  ml-3 mr-3 mb-3">
                            <button type="submit" id="saveDriverDetails" class="btn btn-large btn-primary">ADD DRIVER DETAILS<i class="icon-paperplane ml-2"></i></button>
                        </div>
                        <div class="col-md-12" id="loader2"></div>
                    </div>
                </div>

                
            </div>
        </div>  
    </form>
</div>