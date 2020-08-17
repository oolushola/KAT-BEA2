@extends('layout')

@section('title')Kaya ::. Issue Types @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Transportation</span> - Drivers</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-truck text-primary"></i> <span>View Trucks</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Truck History</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid)) Update @else New @endif Issue Type</h5>
            </div>

            <div class="card-body">
                <form method="POST" name="frmIssueType" id="frmIssueType">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" value="{{$recid->id}}" id="id" />
                        {!! method_field('PATCH') !!}
                    @endif

                    <div id="singleEntryForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Issue Category</label>
                                    <select class="form-control" name="issue_category" id="issueCategory">
                                        <option value="0">Choose Issue Category</option>
                                        <option value="1">On Journey</option>
                                        <option value="2">Offloading</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Issue Type</label>
                                    <input type="text" class="form-control" name="issue_type" id="issueType" value="<?php if(isset($recid)) { echo $recid->issue_type; } ?>" >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" placeholder="" name="description" id="description">@if(isset($recid)){{$recid->description}}@endif</textarea>
                                </div>
                            </div>
                            <div id="messagePlaceholder"></div>
                        </div>

                        <div class="text-right">
                            <span id="loader"></span>
                            @if(isset($recid))
                                <button type="submit" class="btn btn-primary" id="updateIssueType">Update 
                            @else
                                <button type="submit" class="btn btn-primary" id="saveIssueType">Save 
                            @endif
                                Issue Type <i class="icon-paperplane ml-2"></i>
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
                <h5 class="card-title">Issue Type Preview</h5>
            </div>
            <div class="table-responsive" style="max-height:600px; overflow:auto">
                <table class="table table-bordered" id="myTable" style="font-size:11px;">
                    <thead class="table-info">
                        <tr>
                            <th>#</th>
                            <th>Issue Category</th>
                            <th>Issue Type</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                        @if(count($issueTypes))
                        @foreach($issueTypes as $issueType)
                        <?php
                            $counter++;
                            if($issueType->issue_category == 1) { $issuecategory = 'On Journey'; }
                            if($issueType->issue_category == 2) { $issuecategory = 'Offloading'; }
                            if($counter % 2 == 0){ $css = 'table-success'; } else { $css = '';}
                        ?>
                        <tr class="{{$css}}">
                            <td>{{ $counter }}</td>
                            <td>{{ $issuecategory }}</td>
                            <td>{{ $issueType->issue_type }}</td>
                            <td>{{ $issueType->description }}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('issue-types/'.$issueType->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        @if(Auth::user()->role_id == 1)
                                        <i class="icon-trash"></i>
                                        @endif
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td class="table-success" colspan="6">There are no issue types added</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/issueType.js')}}"></script>
@stop
