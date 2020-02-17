@extends('layout')

@section('title')Kaya ::. Loading Clerks @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Fields Ops</span> - Loading Clerks</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>View Clerks</span></a>
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-calendar5 text-primary"></i> <span>Clerk history</span></a>
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
                <h5 class="card-title">@if(isset($recid)) Update @else Add New @endif Clerk</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{URL('loading-clerk')}}" name="frmFieldOps" id="frmFieldOps">
                    @csrf
                    @if(isset($recid))
                    {!! method_field('PATCH') !!}
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                    @endif
                    
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" placeholder="John" name="first_name" id="firstName" value="<?php if(isset($recid)) { echo $recid->first_name; } ?>">
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" placeholder="Doe" name="last_name" id="lastName" value="<?php if(isset($recid)) { echo $recid->last_name; } ?>">
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="number" class="form-control" placeholder="+234-***-***-****" name="phone_no" id="phoneNumber" value="<?php if(isset($recid)) { echo $recid->phone_no; } ?>">
                    </div>

                    <div class="form-group">
                        <label>email</label>
                        <input type="text" class="form-control" placeholder="johndoe@example.com" name="email" id="email" value="<?php if(isset($recid)) { echo $recid->email; } ?>" >
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <select class="form-control" name="location_id" id="location">
                            <option value="0">Choose location</option>
                            @foreach($states as $state)
                                @if(isset($recid))
                                    @if($recid->location_id == $state->regional_state_id)
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
                        @if(isset($recid))
                            @if($recid->field_ops_type == 1)
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input fieldOps" name="fieldOps"  value="1" checked>Loading Clerk
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input fieldOps" name="fieldOps" value="2">Offloading Clerks
                                    </label>
                                </div>
                            @else
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input fieldOps" name="fieldOps"  value="1">Loading Clerk
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input fieldOps" name="fieldOps" value="2" checked>Offloading Clerks
                                    </label>
                                </div>
                            @endif
                        @else

                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input fieldOps" name="fieldOps"  value="1">Loading Clerk
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input fieldOps" name="fieldOps" value="2">Offloading Clerks
                                </label>
                            </div>
                        @endif


                        <input type="hidden" name="field_ops_type" id="fieldOpsType" value="<?php if(isset($recid)){ echo $recid->field_ops_type; } ?>" >
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" placeholder="23 Babatunde Jose, Victoria Island, Lagos." name="address" id="address"><?php if(isset($recid)) {echo $recid->address; } ?></textarea>
                    </div>

                    <div class="text-right">
                        <span id="loader">@include('errors')</span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary" id="updateFieldOps" >Update Clerk Details 
                        @else
                        <button type="submit" class="btn btn-primary" id="addFieldOps" >Add Clerk Details 
                            <i class="icon-paperplane ml-2"></i>
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <!-- /basic layout -->

    </div>

    <div class="col-md-7">
    &nbsp;
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Preview Pane of Field Ops</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 0; ?>
                        @if(count($fieldOps))
                            @foreach($fieldOps as $fieldOfficer)
                            <?php $counter++;
                                $counter % 2 == 0 ? $css = '' : $css = 'table-success'
                            ?>
                            <tr class="{{$css}}" style="font-size:10px">
                                <td>{{$counter}}</td>
                                <td>{{$fieldOfficer->last_name}} {{$fieldOfficer->first_name}}</td>
                                <td>{{$fieldOfficer->email}}</td>
                                <td>{{$fieldOfficer->phone_no}}</td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('loading-clerk/'.$fieldOfficer->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                        <a href="#" class="list-icons-item text-danger-600">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class='table-success'>You've not add any field ops clerk</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/loading-clerk.js')}}"></script>
@stop