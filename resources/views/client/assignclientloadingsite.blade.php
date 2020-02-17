@extends('layout')

@section('title') Kaya ::. Assign loading sites to {{ucwords($clientName)}} @stop

@section('main')
<form name="frmAssignLoadingSite" id="frmAssignLoadingSite">
    @csrf
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Clients</span> - {{ucwords($clientName)}}</h4>
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

        <input type="hidden" name="validator" id="validator" value="0" />
        <input type="hidden" name="client_id" value="{{$client_id}}" />
        <input type="hidden" name="client_name" value="{{$clientName}}" />
        <div class="ml-4 mr-4">
            <div class="form-group">
                <label class="font-weight-semibold">Location</label>
                <select class="form-control form-control-select2" name="state_id" id="state_id">
                    <option value="0">Choose loading location</option>
                    @foreach($states  as $state)
                        <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                    @endforeach
                </select>
                <span id="loader" class="mt-2"></span>
            </div>
        </div>
    </div>

    <div id="contentDropper">
        <div class="row">
            <div class="col-md-5">
            &nbsp;

                <div class="card">
                <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody style="font-size:10px;">
                                <tr>
                                    <td class="table-primary font-weight-semibold" colspan="3">
                                        Assign loading sites to {{ucwords($clientName)}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="2" id="selectAllLeftText">
                                        Select all available loading sites
                                    </td>
                                </tr>
                                @if(count($loadingsites))
                                <?php $counter = 0; ?>
                                    @foreach($loadingsites as $warehouse)
                                    <?php $counter++;
                                    $counter % 2 == 0 ? $css = '' : $css='table-success';
                                    ?>
                                    <tr class="{{$css}}" style="font-size:10px">
                                        <td>
                                            <input type="checkbox" class="loadingSiteLeft" name="loading_site[]" value="{{$warehouse->id}}">
                                        </td>
                                        <td>{{strtoupper($warehouse->loading_site)}}</td>
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
                    <button type="submit" class="btn btn-primary" id="assignLoadingSite">Assign
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger" id="removeAssignedLoadingSite">Remove <i class="icon-point-left ml-2"></i></button>
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
                                    <td class="table-primary font-weight-semibold" colspan="3">Assigned loading sites to {{ucwords($clientName)}}</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/client.js')}}"></script>
@stop