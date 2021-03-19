@extends('layout')

@section('title') Kaya ::. Clients @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>
@stop

@section('main')
<!-- Page header -->
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home</span> - Clients</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default">
                    <i class="icon-calculator text-primary"></i> 
                    <span>View Clients</span>
                </a>
                <a href="#" class="btn btn-link btn-float text-default">
                    <i class="icon-calendar5 text-primary"></i>
                    <span>Client Order History</span>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- /page header -->


<div class="row">
    <div class="col-md-5">
        &nbsp;
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif Client</h5>
            </div>

            <div class="card-body">
                <form action="" name="frmClients" id="frmClients">
                @csrf
                @if(isset($recid))
                {!! method_field('PATCH') !!}
                <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                @endif

                    <div class="form-group">
                        <label class="text-primary">Does this company has a Parent Company?</label><br>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input parentCompanyStatus" name="parentCompanyStatus" value="1" @if(isset($recid) && $recid->parent_company_status == 1) checked @endif>Yes
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input parentCompanyStatus" name="parentCompanyStatus" value="0" @if(isset($recid) && $recid->parent_company_status ==0) checked @endif>No
                            </label>
                        </div>
                        <input type="hidden" name="parent_company_status" id="parent_company_status" value="@if(isset($recid)){{$recid->parent_company_status}} @endif" >
                    </div>

                    <div class="row">
                        <!-- <div id="parentCompanyContainer"> -->
                            <div class="form-group col-md-6">
                                <label>Parent Company</label>
                                <select class="form-control" id="parentCompany" name="parent_company_id">
                                    <option value="0">Choose Parent Company</option>
                                    @foreach($clients as $client)
                                    @if(isset($recid) && $recid->parent_company_status !=0 && $recid->parent_company_id == $client->id))
                                    <option value="{{$client->id}}" selected>{{$client->company_name}}</option>
                                    @else
                                    <option value="{{$client->id}}">{{$client->company_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        <!-- </div> -->

                        <div class="form-group col-md-6">
                            <label>Company Name</label>
                            <input type="text" class="form-control" placeholder="Kayaaafrica Technologies Nig. Limited" name="company_name" id="companyName" value="<?php if(isset($recid)) { echo $recid->company_name; } ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Person of Contact</label>
                            <input type="text" class="form-control" placeholder="John Doe" name="person_of_contact" id="personOfContact" value="<?php if(isset($recid)) { echo $recid->person_of_contact;} ?>"> 
                        </div>

                        <div class="form-group col-md-6">
                            <label>Phone Number</label>
                            <input type="number" class="form-control" placeholder="+234-***-***-****" name="phone_no" id="phone_no" value="<?php if(isset($recid)) { echo $recid->phone_no; } ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="text" class="form-control" placeholder="johndoe@kayaafrica.co" name="email" id="email" value="<?php if(isset($recid)){ echo $recid->email; } ?>">
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label>Country</label>
                            <select class="form-control form-control-select2" name="country_id" id="country">
                                <option value="0">Choose country of Operation</option>
                                @foreach($countries as $country)
                                    @if(isset($recid))
                                        @if($recid->country_id == $country->regional_country_id)
                                        <option value="{{$country->regional_country_id}}" selected>{{$country->country}}</option>
                                        @else
                                        <option value="{{$country->regional_country_id}}">{{$country->country}}</option>
                                        @endif
                                    @else
                                    <option value="{{$country->regional_country_id}}">{{$country->country}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6" id="stateHolder">
                            <label>State</label>
                            <select class="form-control form-control-select2" name="state_id" id="state">
                                <option value="1">State Domiciled</option>
                                @if(isset($recid))
                                        @foreach($states as $state)
                                            @if($state->regional_country_id == $recid->country_id)
                                                @if($state->regional_state_id == $recid->state_id)
                                                    <option value="{{$state->regional_state_id}}" selected>{{$state->state}}</option>
                                                @else
                                                    <option value="{{$state->regional_state_id}}">{{$state->state}}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Address</label>
                            <textarea rows="1" class="form-control" placeholder="23, Babatunde Jose, Victoria Island, Lagos." name="address" id="address"><?php if(isset($recid)) { echo $recid->address; } ?></textarea>
                        </div>
                        <legend class="font-weight-semibold"><i class="icon-coins mr-2"></i>Pay kaya into this bank account</legend>
                        <div class="form-group col-md-4">
                            <label>Bank Name</label>
                            <input type="text" class="form-control font-size-sm" placeholder="Sterling Bank Nig. Plc." name="bank_name_payment" value="<?php if(isset($recid)){ echo $recid->bank_name_payment; } ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Account Name</label>
                            <input type="text" class="form-control font-size-sm" placeholder="KAT" name="account_name_payment" value="<?php if(isset($recid)){ echo $recid->account_name_payment; } ?>">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Account No</label>
                            <input type="text" class="form-control font-size-sm" placeholder="9876543210" name="account_no_payment" value="<?php if(isset($recid)){ echo $recid->account_name_payment; } ?>">
                        </div>
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary" id="updateClientDetails">Update Client Details
                        @else
                        <button type="submit" class="btn btn-primary" id="addClientDetails">Save Client Details
                        @endif
                        <i class="icon-paperplane ml-2"></i>
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>

    </div>

    <div class="col-md-7">
        &nbsp;
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Preview Pane of Clients</h5>
            </div>
            {{$clients->links()}}

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info" style="font-size:9px;">
                        <tr>
                            <th>#</th>
                            <th>Client Information</th>
                            <th>
                                <button id="updateClientExpectedMargin">Update Expected Margin</button>
                            </th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form method="POST" id="updateClientMonthlyTarget">
                            @csrf
                        <?php $counter = 0; ?>
                        @if(count($clients))
                        @foreach($clients as $client)
                        <?php
                            $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                            <tr class="{{$css}}" style="font-size:10px;">
                                <td>{{$counter}}</td>
                                <td>
                                    {{strtoupper($client->company_name)}}  <br>
                                    <span class="badge badge-success">{{ucwords($client->person_of_contact)}} {{$client->phone_no}}</span><br>
                                    
                                    @if($client->account_no_payment) 
                                    <span class="badge badge-primary">Pays to: {{ ucfirst($client->bank_name_payment) }}, {{ ucfirst($client->account_no_payment) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="hidden" name="clientId[]" value="{{$client->id}}">
                                    <?php
                                        if($client->expected_margin) {
                                            $expectedMargin = $client->expected_margin;
                                        }
                                        else {
                                            $expectedMargin = 10000000;
                                        }
                                    ?>
                                    <input type="text" name="expectedMonthlyMargin[]" style="border: 1px solid #ccc; padding: 5px; outline:none" value="{{number_format($expectedMargin,2)}}">
                                </td>
                                <td>
                                    <div class="list-icons">
                                        <a href="{{URL('client-products/'.str_slug($client->company_name).'/'.$client->id)}}" target="_blank" class="list-icons-item" title="Add client product">
                                            <i class="icon-basket"></i>
                                        </a>
                                        <a href="{{URL('/client-fare-rates/'.str_slug($client->company_name).'/'.$client->id)}}" class="list-icons-item text-info-600" title="Add fare ratings" target="_blank">
                                            <i class="icon-coins"></i>
                                        </a>
                                        <a href="{{URL('client-loading-site/'.str_slug($client->company_name).'/'.$client->id)}}" class="list-icons-item text-warning-600" title="Assign loading site to this client" target="_blank">
                                            <i class="icon-cog"></i>
                                        </a>
                                        <a href="{{URL('clients/'.$client->id.'/edit')}}" class="list-icons-item text-primary-600" title="Edit this client information"><i class="icon-pencil7"></i></a>
                                    <a href="#" class="list-icons-item text-danger-600" title="Delete this client">
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td class="6" colspan="table-secondary">You've have not created any information</td>
                            </tr>
                        @endif
                        </form>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/client.js')}}"></script>
@stop