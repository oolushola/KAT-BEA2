@extends('layout')

@section('title') Kaya ::. Assign ad-hoc staff to a region @stop

@section('main')
<form name="frmUserAssignedRegion" id="frmUserAssignedRegion">
    @csrf
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Ad-hoc</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none">
                    <i class="icon-more"></i>
                </a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#" id="closeBtn" class="btn btn-link btn-float text-default">
                        <i class="icon-x text-primary"></i> <span>Close</span>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="ml-4 mr-4 row">
            <div class="form-group col-md-4">
                <label class="font-weight-semibold">Region / Province / State</label>
                <select class="form-control form-control-select2" name="regional_state_id" id="regional_state_id">
                    <option value="0">Choose a state</option>
                    @foreach($states as $stateName)
                    <option value="{{$stateName->regional_state_id}}">{{$stateName->state}}</option>
                    @endforeach
                    
                </select>
                <span id="loader" class="mt-2"></span>
            </div>
            <div class="form-group col-md-4">
                <label class="font-weight-semibold">Ad-hoc Staff</label>
                <select class="form-control form-control-select2" name="user_id" id="user_id">
                    <option value="0">Choose an Ad-hoc Staff</option>
                    @foreach($users as $adhocStaff)
                    <option value="{{$adhocStaff->id}}">{{$adhocStaff->first_name}} {{$adhocStaff->last_name}}</option>
                    @endforeach
                    
                </select>
                <span id="loader" class="mt-2"></span>
            </div>
            
        </div>
    </div>

    <input type="hidden" value="0" id="validator">

    <div id="contentDropper">
        <div class="row">
            <div class="col-md-5">
            &nbsp;

                <div class="card" >
                    <div class="table-responsive" style="max-height:450px">
                        <table class="table table-bordered">
                            <tbody style="font-size:10px;">
                                <tr>
                                    <td class="table-primary font-weight-semibold" colspan="3">
                                        Assign Ad-hoc to Region
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="2" id="selectAllLeftText">
                                        Select all available states
                                    </td>
                                </tr>
                                @if(count($exactDestionations))
                                <?php $counter = 0; ?>
                                    @foreach($exactDestionations as $exactDestionation)
                                    <?php $counter++;
                                    $counter % 2 == 0 ? $css = '' : $css='table-success';
                                    ?>
                                    <tr class="{{$css}}" style="font-size:10px">
                                        <td>
                                            <input type="checkbox" class="statesLeft" name="exactDestination[]" value="{{$exactDestionation->id}}">
                                        </td>
                                        <td>{{strtoupper($exactDestionation->transporter_destination)}}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>No loading site available to assign</td>
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
                    <button type="submit" class="btn btn-primary" id="assignLocations">Assign
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger" id="removeAssignedLocations">Remove <i class="icon-point-left ml-2"></i></button>
                </div>
            </div>

            <div class="col-md-5">
            &nbsp;

                <!-- Contextual classes -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody style="font-size:10px;">
                                <tr>
                                    <td class="table-primary font-weight-semibold" colspan="3">Assigned Users to Region</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%"><input type="checkbox"></td>
                                    <td class="table-info font-weight-semibold" colspan="2">Select all assigned loading sites</td>
                                </tr>
                                <tr class="table-success" style="font-size:10px">
                                    <td colspan="2" class="font-weight-semibold">You've not assigned any loading site for this client</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /contextual classes -->


            </div>

            
        </div>
    </div>
</form>
@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/assignAdhocStaff.js')}}"></script>
@stop