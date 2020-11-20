<div class="modal fade transloader">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="text-primary mt-md-2 font-weight-semibold d-inline" id="selectedTripPlaceHolder">KAID0001</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        &nbsp;
                        <form method="POST" id="frmTripTransload" name="frmTripTransload">
                            @csrf
                            <!-- Basic layout-->
                            <div class="card">
                                <div class="card-body">
                                <input type="hidden" name="transload_trip_id" id="transloadTripId" />    
                                <input type="hidden" name="current_truck_id" id="previousTruckId" />
                                <input type="hidden" name="current_driver_id" id="previousDriverId" />
                                <input type="hidden" name="transporter_id" id="transporterId" />
                                

                                    <div id="journeyContainer">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Previous Truck Information</label>
                                                    <input type="text" class="form-control" id="previousTruckNo" style="border-radius:0px;" value="" disabled>

                                                    <input type="text" class="form-control" id="previousTruckType" placeholder="" style="border-radius:0" value="" disabled>

                                                    <input type="text" class="form-control" id="previousTonnage" style="border-radius:0" value="" disabled>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Previous Driver Information</label>
                                                    <input type="text" class="form-control" id="previousDriverName" style="border-radius:0px;" value="Odejobi Olushola D.">

                                                    <input type="text" class="form-control" id="previousDriverNo" style="border-radius:0px;" value="">

                                                    <input type="text" class="form-control" id="previousMotorBoy" style="border-radius:0px;" value="">
                                                </div>
                                            </div>
                                                
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>New Truck Information</label>
                                                    <input type="text" class="form-control" name="truck_no" id="truckNo" style="border-radius:0px;" placeholder="ABC 123 XY">

                                                    <select class="form-control" name="truck_type" id="truckType" style="border-radius:0">
                                                        <option value="0">Choose Truck Type</option>
                                                        @foreach($truckTypes as $truckType)
                                                        <option value="{{ $truckType->truck_type }}">{{ $truckType->truck_type }}</option>
                                                        @endforeach
                                                    </select>

                                                    <select class="form-control" name="tonnage" id="tonnage" style="border-radius:0">
                                                        <option value="0">Choose Tonnage</option>
                                                        @foreach($tonnages as $tonnage)
                                                        <option value="{{ $tonnage->tonnage }}">{{ $tonnage->tonnage }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 driverDetailsChanged">
                                                <div class="form-group">
                                                    <label>Transload with same driver? <input type="checkbox" id="sameDriverChecker" name="sameDriverChecker" checked /></label>
                                                    <input type="text" class="form-control" name="driverName" id="tDriverName" style="border-radius:0px;"  placeholder="Full Name">

                                                    <input type="text" class="form-control" name="driverNo" id="tdriverNo" style="border-radius:0px;"  placeholder="Phone No">

                                                    <input type="text" class="form-control" name="motorboyInfo" style="border-radius:0px;" placeholder="Motor Boy Info">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <label class="label">Reason for Transloading</label>
                                    <input type="text" value="" name="transloading_comment" id="transloadinComment" style="width:100%; margin:2px; border:1px solid #ccc; padding:10px; outline: none; font-size: 18px " />

                                    <div class="text-right">
                                        <span id="placeholderLabel"></span>
                                        <button type="submit" class="btn btn-primary font-size-sm font-weight-semibold" id="transload">TRANSLOAD
                                            <i class="icon-git ml-1"></i>
                                        </button>
                                    </div>
                                    
                                </div>
                            </div>
                            <!-- /basic layout -->
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" id="previewLog"></div>
                </div>
            </div>
        </div>
    </div>  
</div>