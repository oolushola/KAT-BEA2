@extends('layout')

@section('title')Kaya ::. Pair Loading Site With Field Ops @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Dashboard</span> - Pair Loading Sites</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-truck text-primary"></i> <span>Products</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Truck Types</span></a>
            </div>
        </div>
    </div>
</div>

<form name="frmPairOpsLoadingSite" id="frmPairOpsLoadingSite" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Choose a Person </label>
                <select class="form-control" id="person" name="person">
                    <option value="0">Select</option>
                    @foreach($people as $person)
                    <option value="{{$person->id}}">{{ $person->first_name }} {{ $person->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>  
    <div id="contentDropper">
        <div class="row">
            <div class="col-md-5">
                &nbsp;
                <div class="card" >
                    <div class="table-responsive" style="max-height:1050px">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="5">Loading Site Lists</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllLeftText">
                                        Select Available loading sites
                                    </td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="assingLoadingSite">
                                @if(count($loadingSites))
                                <?php $count = 0; ?>
                                @foreach($loadingSites as $key => $loadingSite)
                                <?php $count++; if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } ?>
                                    <tr class="{{ $cssStyle }}">
                                        <td>
                                            <input type="checkbox" value="{{ $loadingSite->id }}" class="availableLoadingSite" name="loadingSites[]" />
                                        </td>
                                        <td>{{ $loadingSite->loading_site }}</td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr class="table-success" style="font-size:10px">
                                        <td colspan="2" class="font-weight-semibold">You do not have any loading site yet</td>
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
                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="assignLoadingSite">PAIR
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeLoadingSite">REMOVE <i class="icon-point-left ml-2"></i></button>
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
                                    <td class="table-primary font-weight-bold font-size-sm" colspan="4">Assigned loading sites</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all assigned loading sites</td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;">
                                
                                <tr class="table-success" style="font-size:10px">
                                    <td colspan="2" class="font-weight-semibold">You've not assigned any loading site yet.</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/peopleloadingSitePair.js?v=').time()}}"></script>
@stop
