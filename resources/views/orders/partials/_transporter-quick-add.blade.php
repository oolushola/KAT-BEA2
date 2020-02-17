<div id="transporterQuickAdd" class="modal fade">
    @csrf
    <form method="POST" name="frmTransporter" id="frmTransporter">
        @csrf
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Add - TRANPORTER<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="row ml-3 mr-3 mt-3 show">
                        <div class="col-md-6">
                            <legend class="font-weight-semibold"><i class="icon-person mr-2"></i> Personal Information</legend>

                            <div class="form-group">
                                <label>Transport Name</label>
                                <input type="text" class="form-control" placeholder="Dangote Sinotrucks" name="transporter_name" id="transporterName" value="<?php if(isset($recid)){ echo $recid->transporter_name; }  ?>" >
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" placeholder="johndoe@kayaafrica.co" name="email" id="email" value="<?php if(isset($recid)) { echo $recid->email;} ?>">
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="number" class="form-control" placeholder="+234-***-***-****" name="phone_no" id="phoneNumber" value="<?php if(isset($recid)) { echo $recid->phone_no; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" placeholder="23 Babatunde Jose, Victoria Island, Lagos." name="address" id="address"><?php if(isset($recid)) { echo $recid->address; } ?></textarea>
                            </div>

                        </div>
                        
                        <div class="col-md-6">
                        
                            <legend class="font-weight-semibold"><i class="icon-piggy-bank mr-2"></i> Bank Details</legend>

                            <div class="form-group">
                                <label>Bank Name</label>
                                <input type="text" class="form-control" placeholder="Sterling Bank Nig. Plc" name="bank_name" id="bankName" value="<?php if(isset($recid)){ echo $recid->bank_name; }  ?>" >
                            </div>

                            <div class="form-group">
                                <label>Account Name</label>
                                <input type="text" class="form-control" placeholder="+234-***-***-****" name="account_name" id="accountName" value="<?php if(isset($recid)) { echo $recid->account_name; } ?>">
                            </div>

                            <div class="form-group">
                                <label>Account No.</label>
                                <input type="text" class="form-control" placeholder="0099009900" name="account_number" id="accountNumber" value="<?php if(isset($recid)) { echo $recid->account_number;} ?>">
                            </div>
                        
                        </div>
                        <div class="text-left  ml-3 mr-3 mb-3">
                            <button type="submit" id="addTransporter" class="btn btn-large btn-primary">ADD TRANSPORTER<i class="icon-paperplane ml-2"></i></button>
                        </div>
                        <div class="col-md-12" id="loader"></div>

                    </div>
                </div>

                
            </div>
        </div>  
    </form>
</div>