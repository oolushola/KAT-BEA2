<div id="truckAvailabilityStatusChange" class="modal fade">
    @csrf
    <form method="POST" name="frmTruckAvailabilityUpdate" id="frmTruckAvailabilityUpdate">
        @csrf {!! method_field('PATCH') !!}
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="textDescriptor" style="font-size:11px;">TRUCK AVAILABILITY - STATUS UPDATE<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="row  mr-3 show">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Current Status" name="truck_status" id="truckStatus">
                                <input type="hidden" name="truck_availability_id" id="truckAvailabilityId">
                            </div>
                        </div>
                        
                        <div class="text-left ml-2">
                            <button type="submit" id="updateAvailabilityStatus" class="btn btn-large btn-primary">Update Status<i class="icon-paperplane"></i></button>
                        </div>
                        <div class="col-md-12" id="loader2"></div>
                    </div>
                </div>

                
            </div>
        </div>  
    </form>
</div>