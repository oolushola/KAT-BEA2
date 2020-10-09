<div id="previewPR" class="modal fade">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header" style="padding:5px; background:#324148">
                <h5 class="font-weight-sm font-weight-bold text-warning" id="selectUserPr">&nbsp;</h5>
                <span class="ml-2"></span>

                <button class="btn btn-primary font-size-xs ml-1 font-weight-bold" id="jobDescription" data-user="{{ $user->id }}">Job Description</button>
                <button class="btn btn-primary font-size-xs ml-1 font-weight-bold" id="reviewPr" data-user="{{ $user->id }}">Review</button>
                <button class="btn btn-primary font-size-xs ml-1 font-weight-bold" id="ecdpPr" data-user="{{ $user->id }}">E.C.D.P</button>

                <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
            </div>
            
            <div class="modal-body" id="modalBody"> 
                <div class="row">
                    <div class="col-md-12 col-sm-12 mb-2" id="prMasterPlaceholder"></div>
                </div>
            </div>

        </div>
    </div>  
</div>














