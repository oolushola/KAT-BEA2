<form name="frmAssignAccountManager" id="frmAssignAccountManager" method="POST">
    @csrf
    <div id="assignAccountManager" class="modal fade" >
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#324148">
                    <h5 class="font-weight-sm font-weight-bold text-warning">
                        <span id="selectedStatus">Assign Account Manager to a Client 
                        <select style="outline:none; padding:10px; font-size:12px; border: 1px solid #ccc" name="user" id="accountManager">
                            <option value="0">Choose Unit Head</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                        </span>
                    </h5>
                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                </div>
                
                <div class="modal-body"> 
                    <input type="hidden" value="0" id="validator">
                    <p class="m-0 mt-2" id='loader2'></p>
                    <div id="contentDropper">
                        <div class="row">
                            <div class="col-md-5">
                                &nbsp;
                                <div class="card" >
                                    <div class="table-responsive" style="max-height:1050px">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td class="table-primary font-weight-bold font-size-sm" colspan="5">CLIENTELE</td>
                                                </tr>
                                                <tr>
                                                    <td class="table-info" width="10%">
                                                        <input type="checkbox" id="selectAllLeft">
                                                    </td>
                                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllLeftText">
                                                        Select all clients
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size:10px;" class="assignClient">
                                            @if(count($clientele))
                                                <?php $count = 0; ?>
                                                @foreach($clientele as $key => $client)

                                                <?php $count++; if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } ?>
                                                    <tr class="{{ $cssStyle }}">
                                                        <td>
                                                            <input type="checkbox" value="{{ $client->id }}" class="availableClient" name="clientele[]" />
                                                        </td>
                                                        <td>{{ $client->company_name }}</td>
                                                    </tr>
                                                @endforeach
                                                @else
                                                    <tr class="table-success" style="font-size:10px">
                                                        <td colspan="2" class="font-weight-semibold">You do not have any client yet</td>
                                                    </tr>
                                                @endif
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                &nbsp;
                                <div class="text-center mt-5">
                                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="assignClient">ASSIGN
                                        <i class="icon-point-right ml-2"></i>
                                    </button>
                                    <br /><br />
                                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeClient">REMOVE <i class="icon-point-left ml-2"></i></button>
                                </div>
                            </div>

                            <div class="col-md-5">
                                &nbsp;
                                <!-- Contextual classes -->
                                <div class="card">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td class="table-primary font-weight-bold font-size-sm" colspan="4">ASSIGNED CLIENTS</td>
                                                </tr>
                                                <tr>
                                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all assigned clients</td>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size:10px;" class="badgeAndAvailableTrips">
                                                
                                                <tr class="table-success" style="font-size:10px">
                                                    <td colspan="2" class="font-weight-semibold">You've not assigned any client yet.</td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /contextual classes -->


                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</form>


