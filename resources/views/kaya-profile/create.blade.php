@extends('layout')

@section('title')Kaya ::. Profiling @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Preference Settings</span> - Kaya Profile</h4>
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
                <h5 class="card-title">@if(isset($recid)) Update @else Add @endif Company Profile</h5>
            </div>

            <ul style="margin:0; padding:0; margin-left:20px;">
                <li class="tabs tabs-default" id="basciInfoTab">Basic Information</li>
                <li class="tabs" id="bankTab">Bank Details</li>
                <li class="tabs" id="guarantorTab">Authorized Signatory </li>
            </ul>

            <div class="card-body">
                <form method="POST" name="frmKayaProfiling" id="frmKayaProfiling" action="{{URL('companies-profile')}}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($recid))
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                    {!! method_field('PATCH') !!}
                    @endif

                    @if(count($errors)>0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="basicInfoDetails show">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control" name="company_name" id="company_name" value="@if(isset($recid)){{$recid->company_name}}@else{{ old('company_name') }}@endif" >
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="company_email" id="company_email" value="@if(isset($recid)){{ $recid->company_email }}@else{{ old('company_email') }}@endif">
                        </div>

                        <div class="form-group">
                            <label>Website (URL)</label>
                            <input type="text" class="form-control" name="website" id="website" value="@if(isset($recid)){{ $recid->website }}@else{{ old('website') }}@endif">
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="number" class="form-control" name="company_phone_no" id="phoneNumber" value="@if(isset($recid)){{ $recid->company_phone_no }}@else{{ old('company_phone_no') }}@endif">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control" placeholder="23 Babatunde Jose, Victoria Island, Lagos." name="address" id="address">@if(isset($recid)) {{ $recid->address }}@lse{{ old('address') }}@endif</textarea>
                        </div>

                        <div class="form-group">
                            <label>Logo</label>
                            <input type="file" name="company_logo" >
                        </div>

                    </div>

                    <div class="bankDetails hidden">
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" placeholder="Sterling Bank Nig. Plc" name="bank_name" id="bankName" value="@if(isset($recid)){{ $recid->bank_name }}@else{{old('bank_name')}}@endif" >
                        </div>

                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" class="form-control" placeholder="+234-***-***-****" name="account_name" id="accountName" value="@if(isset($recid)){{$recid->account_name}}@else{{old('account_name')}}@endif">
                        </div>

                        <div class="form-group">
                            <label>Account No.</label>
                            <input type="text" class="form-control" placeholder="xxxxxxxxxx" name="account_no" id="accountNumber" value="@if(isset($recid)){{$recid->account_no}}@else{{ old('account_number') }}@endif">
                        </div>

                        <div class="form-group">
                            <label>Tax Identification No. (TIN)</label>
                            <input type="text" class="form-control" placeholder="xxxxxxxxxxx-xxxxx" name="tin" id="tin" value="@if(isset($recid)){{ $recid->tin }}@else{{old('tin')}}@endif">
                        </div>
                    </div>

                    <div class="guarantorDetails hidden">
                        <div class="form-group">
                            <label>Choose Staff</label>
                            <select class="form-control" name="authorized_user_id" id="authorized_user_id" value="{{old('authorized_user_id')}}">
                                <option value="">Choose an Authorized Signatory</option>
                                @foreach($authorizedUsers as $user)
                                <option value="{{$user->id}}">
                                    {{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Upload Signature</label>
                            <input type="file" name="signatory">
                        </div>
                    </div>
                    

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                        <button type="submit" class="btn btn-primary" id="updateCompanyProfile">Update Profile
                        @else
                        <button type="submit" class="btn btn-primary" id="addCompanyProfile">Save Profile
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
                <h5 class="card-title">Preview Pane of Kaya Profile</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    @if(count($companyProfileDetails))
                    @foreach($companyProfileDetails as $kayaProfile)
                    <tr class="table-success font-weight-semibold" style="font-size:10px">
                        <td>{{$kayaProfile->company_name}}</td>
                        <td>{{$kayaProfile->company_email}}</td>
                        <td>{{$kayaProfile->company_phone_no}}</td>
                        <td><a href="{{URL('/companies-profile/'.$kayaProfile->id.'/edit')}}"><i class="icon icon-pencil"></a></td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td>Company Profile Not Updated</td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/jquery.form.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/transporter.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validator/company-profile.js')}}"></script>
@stop