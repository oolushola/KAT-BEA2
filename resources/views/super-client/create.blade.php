@extends('layout')

@section('title')Kaya ::. Super Clients @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Preference</span> - Super Client</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <!-- <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-truck text-primary"></i> <span>View Trucks</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Truck History</span></a>
            </div>
        </div> -->
    </div>
</div>

<div class="row">
    <div class="col-md-5">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif New CLIENT</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{URL('super-client')}}">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" value="{{$recid->id}}" id="id" />
                        {!! method_field('PATCH') !!}
                    @endif


                    <div id="singleEntryForm">

                        <div class="form-group">
                            <label>Super Client Name</label>
                            <input type="text" class="form-control" placeholder="" name="parent_name" value="<?php if(isset($recid)) { echo $recid->parent_name; } ?>" >
                        </div>

                        

                        <div class="text-right">
                            <span id="loader"></span>
                            @if(isset($recid))
                                <button type="submit" class="btn btn-primary" id="updateSuperClient">Update 
                            @else
                                <button type="submit" class="btn btn-primary" id="saveSuperClient">Save 
                            @endif
                                Driver's Detail <i class="icon-paperplane ml-2"></i>
                            </button>
                        </div>
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
                <h5 class="card-title">Preview Pane of SUPER CLIENTS</h5>
            </div>

            <input type="text" id="myInput" placeholder="Search">

            <div class="table-responsive" style="max-height:600px; overflow:auto">
                <table class="table table-bordered" id="myTable">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>SUPER CLIENT</th>
                            
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($superClients))
                        @foreach($superClients as $client)
                        <?php $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                        <tr class="{{$css}}" style="font-size:10px">
                            <td>{{$counter}}</td>
                            <td>{{ucwords($client->parent_name)}}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('super-client/'.$client->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="table-success" colspan="6">You've not added any driver details.</td>
                        </tr>
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
@stop
