<div id="uploadPhotoBox" class="modal fade">
    @csrf
    <form method="POST" name="frmUploadProfilePhoto" id="frmUploadProfilePhoto" enctype="multipart/form-data" action="{{URL('upload-profile-photo')}}">
        @csrf
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Profile Photo<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <input type="hidden" name="fullname" value="{{Auth::user()->last_name}} {{Auth::user()->first_name}}">
                <input type="hidden" name="user" value="{{base64_encode(Auth::user()->id)}}">

                
                <div class="modal-body">
                    <div class="row ml-3 mr-3 mt-3 show">
                                            
                        <div class="col-md-12">
                            <legend class="font-weight-semibold"><i class="icon-file-picture2 mr-2"></i>Personal Information Update</legend>
                        </div>

                            
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="file" class="form-control" name="file" id="file">
                                <input type="hidden" name="filecheck" id="filecheck" value="0" /> 
                                <input type="hidden" name="ftype" id="ftype" value="png,jpg,jpeg,svg,gif" />
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="text-left  ml-3 mr-3 mb-3">
                                <button type="submit" id="updateProfilePhoto" class="btn btn-large btn-primary">Update Profile Photo<i class="icon-file-picture ml-2"></i></button>
                            </div>
                        </div>
                        
                        <div class="col-md-12" id="loader"></div>
                    </div>
                </div>

                
            </div>
        </div>  
    </form>
</div>

