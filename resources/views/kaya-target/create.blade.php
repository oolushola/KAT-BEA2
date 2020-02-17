@extends('layout')

@section('title')Kaya ::. Target @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Preference</span> - Targets</h4>
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
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif Target</h5>
            </div>

            <div class="card-body">
                <div class="form-group">
                    <label>Current Year: </label>
                    <span class="font-weight-semibold text-primary">{!! date('Y') !!}</span>
                    <label class="ml-5">Current Month:</label>
                    <span class="font-weight-semibold text-primary">{!! date('F') !!}</span>
                </div>
                

                <form method="POST" name="frmTarget" id="frmTarget">
                    @csrf
                    @if(isset($recid))
                        <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                        {!! method_field('PATCH') !!}
                    @endif
                    

                    <input type="hidden" name="current_year" value="{{date('Y')}}">
                    <input type="hidden" name="current_month" value="{{date('F')}}">
                    
                    <div class="form-group">
                        <label>Target for the Month</label>
                        <input type="text" class="form-control" placeholder="100" name="target" id="monthlyTarget" value="<?php if(isset($recid)) { echo strtoupper($recid->target); } ?>">
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary" id="updateTarget">Update 
                        @else
                        <button type="submit" class="btn btn-primary" id="addTarget">Add  
                        @endif Target
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
                <h5 class="card-title">Preview Pane of Monthly Target for the Year {!! date('Y') !!}</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th width="5%">#</th>
                            <th>Month</th>
                            <th>Target</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        @if(count($monthlytargets))
                            @foreach($monthlytargets as $target)
                            <?php $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                            ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>{{strtoupper($target->current_month)}}, {{strtoupper($target->current_year)}}</td>
                                <td>{{strtoupper($target->target)}}</td>
                                <td>
                                        <?php 
                                            $currentMonth = date('F'); 
                                            $currentYear = date('Y');
                                            if($currentMonth == $target->current_month && $currentYear == $target->current_year){
                                        ?>
                                    <div class="list-icons">
                                        <a href="{{URL('kaya-target/'.$target->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">You've not added any monthly target.</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/target.js')}}"></script>
@stop
