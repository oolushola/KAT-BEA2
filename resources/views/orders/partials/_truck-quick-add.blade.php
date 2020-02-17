<div id="truckQuickAdd" class="modal fade">
    <form method="POST" name="frmTrucks" id="frmTrucks">
        @csrf
        
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Add - TRUCK<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    
                    <div class="form-group">
                        <label>Transporter</label>
                        <select class="form-control" name="transporter_id" id="transporterId">
                            <option value="0">Choose Transporter</option>
                            @foreach($transporters as $transporter)
                                @if(isset($recid))
                                    @if($recid->transporter_id == $transporter->id)
                                    <option value="{{$transporter->id}}" selected>{{$transporter->transporter_name}}</option>
                                    @else
                                    <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
                                    @endif
                                @else
                                <option value="{{$transporter->id}}">{{$transporter->transporter_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Truck Type</label>
                        <select class="form-control" name="truck_type_id" id="truckTypeId">
                            <option value="0">Choose Truck Type and Tonnage</option>
                            @foreach($truckTypes as $truckType)
                                @if(isset($recid))
                                    @if($recid->truck_type_id == $truckType->id)
                                    <option value="{{$truckType->id}}" selected>
                                        {{$truckType->truck_type}} : {{$truckType->tonnage/1000}} tons
                                    </option>
                                    @else
                                    <option value="{{$truckType->id}}">
                                        {{$truckType->truck_type}} : {{$truckType->tonnage/1000}} tons
                                    </option>
                                    @endif
                                @else
                                <option value="{{$truckType->id}}">
                                    {{$truckType->truck_type}} : {{$truckType->tonnage/1000}} tons
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Truck No</label>
                        <input type="text" class="form-control" placeholder="IKD 445 LD" name="truck_no" id="truckNumber" value="<?php if(isset($recid)) { echo strtoupper($recid->truck_no); } ?>">
                    </div>

                    <div class="text-left  ml-3 mr-3 mb-3">
                        <button type="submit" id="addTruck" class="btn btn-large btn-primary">ADD TRUCK DETAILS<i class="icon-paperplane ml-2"></i></button>
                    </div>
                    <div id="loader3"></div>
                </div>

                
            </div>
        </div>  
    </form>
</div>