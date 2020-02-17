<div id="changePassword" class="modal fade">
    @csrf
    <form method="POST" name="frmChangePassword" id="frmChangePassword">
        @csrf        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                <input type="hidden" name="userIdentification" value="{{base64_encode(Auth::user()->id)}}">

                    <div class="row ml-3 mr-3 mt-3 show">
                                            
                        <div class="col-md-12">
                            <legend class="font-weight-semibold"><i class="icon-lock mr-2"></i>Update Account Password</legend>
                        </div>

                            
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="password" Placeholder="Enter Previous Account Password" class="form-control" id="oldPassword" name="old_password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="password" Placeholder="New Password" class="form-control" id="newPassword" name="new_password">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="password" Placeholder="Confirm New Password" class="form-control" id="confirmNewPassword" name="confirm_new_password">
                            </div>
                        </div>
                        
                        <div class="text-left  ml-3 mr-3 mb-3">
                            <button type="submit" id="changeAccountPassword" class="btn btn-large btn-primary">Update Password<i class="icon-paperplane ml-2"></i></button>
                        </div>
                        
                        <div class="col-md-12" id="loader2"></div>
                    </div>
                </div>

                
            </div>
        </div>  
    </form>
</div>