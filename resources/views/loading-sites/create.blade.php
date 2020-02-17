@extends('layout')

@section('title')Kaya ::. Loading Sites @stop
@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Global Operation</span> - Loading Sites</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        
    </div>
</div>

<div class="row">
    <div class="col-md-5">
    &nbsp;
        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid))Update @else Add @endif Loading Site</h5>
            </div>

            <div class="card-body">
                <form action="" id="frmLoadingSite" name="frmLoadingSite">
                    @csrf
                    @if(isset($recid))
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                    {!! method_field('PATCH') !!}
                    @endif
                    <div class="form-group">
                        <label>State Domiciled</label>
                        <select class="form-control" name="state_domiciled" id="stateDomiciled">
                            <option value="0">Choose State</option>
                            @foreach($states as $state)
                                @if(isset($recid))
                                    @if($state->regional_state_id == $recid->state_domiciled)
                                        <option value="{{$state->regional_state_id}}" selected>{{$state->state}}</option>
                                    @else
                                        <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                                    @endif
                                @else
                                    <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Loading Site Code</label>
                        <input type="text" class="form-control" placeholder="TLV" name="loading_site_code" id="loadingSiteCode" maxlength="3" value="<?php if(isset($recid)) { echo $recid->loading_site_code; } ?>">
                    </div>

                    <div class="form-group">
                        <label>Loading Site</label>
                        <input type="text" class="form-control" placeholder="GPF-APAAP" name="loading_site" id="loadingSite" value="<?php if(isset($recid)){ echo $recid->loading_site; } ?>" />
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" placeholder="23, Babatunde Jose, Victoria Island, Lagos." name="address" id="address"><?php if(isset($recid)){ echo $recid->address; } ?></textarea>
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateLoadingSite">Update Loading Site 
                        @else
                            <button type="submit" class="btn btn-primary" id="addLoadingSite">Add Loading Site 
                        @endif
                        <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /basic layout -->

    </div>

    <div class="col-md-7">
    &nbsp;

        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Preview Pane of Truck Types</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th width="20%">Loading Site</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($loadingSites))
                        @foreach($loadingSites as $loading_sites)
                        <?php $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                        <tr class="{{$css}}" style="font-size:10px">
                            <td>{{$counter}}</td>
                            <td width="20%S">{{$loading_sites->loading_site}}</td>
                            <td>{{$loading_sites->address}}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('loading-sites/'.$loading_sites->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else

                    @endif
                        
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /contextual classes -->


    </div>
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/loading-site.js')}}"></script>
@stop
