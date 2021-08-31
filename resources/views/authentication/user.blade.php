@extends('layout')

@section('title') User Registration @stop

@section('main')

<!-- Page header -->
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Staff</span> - Onboarding</h4>
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
&nbsp;

<div class="row">
    <div class="col-md-5">
        <div class="card mb-0">
            <div class="card-body">
                @if(isset($recid))
                <form method="POST" action="{{URL('user-registration', $recid->id)}}" name="frmUserRegistration" id="frmUserRegistration">

                @else
                    <form method="POST" action="{{URL('registeruser')}}" name="frmUserRegistration" id="frmUserRegistration">
                @endif
                    @csrf
                    @if(isset($recid))
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}" >
                    {!! method_field('PATCH') !!}
                    @endif

                    <div class="form-group text-center text-muted content-divider">
                        <span class="px-2 font-weight-semibold">Basic Information</span>
                    </div>
                    <span id="errorLoader"></span>
                        @if(count($errors)>0)
                            @foreach($errors->all() as $error) 
                                <div class="text-danger"><i class="icon-cancel-circle2 mr-2"></i> {{$error}}</div>
                            @endforeach
                        @endif
                    

                    <div class="form-group">
                        <input type="text" class="form-control" name="first_name" id="firstName" placeholder="First Name" value="@if(isset($recid)){{$recid->first_name}}@endif">
                        <span id="firstNameError"></span>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="last_name" id="lastName" placeholder="Last Name" value="@if(isset($recid)){{$recid->last_name}}@endif">
                        <span id="lastNameError"></span>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="@if(isset($recid)){{$recid->email}}@endif">
                        <span id="emailError"></span>
                    </div>

                    <div class="form-group">
                        <input type="text" onkeypress="return isNumber(event)" class="form-control" name="phone_no" id="phoneNumber" placeholder="Phone Number" value="@if(isset($recid)){{$recid->phone_no}} @endif">
                        <span id="phoneNumberError"></span>
                    </div>

                    <div class="form-group">
                        <select name="department_id" id="department" class="form-control">
                            <option value="0">Choose Department</option>
                            @foreach($departments as $department)
                            @if(isset($recid) && $recid->department_id === $department->id)
                                <option value="{{$department->id}}" selected>{{$department->department}}</option>
                            @else
                                <option value="{{$department->id}}">{{$department->department}}</option>
                            @endif
                            @endforeach
                        </select>
                        <span id="departmentError"></span>
                    </div>

                    <div class="form-group text-center text-muted content-divider">
                        <span class="px-2 font-weight-semibold">Role Specification</span>
                    </div>

                    <span id="roleSpecificationError"></span>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input userRole" name="userRole"  value="1" @if(isset($recid) && $recid->role_id == 1) checked @endif>Super Admin
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input userRole" name="userRole" value="2" @if(isset($recid) && $recid->role_id == 2) checked @endif>Admin
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input userRole" name="userRole" value="3" @if(isset($recid) && $recid->role_id == 3) checked @endif>Finance
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input userRole" name="userRole" value="4" @if(isset($recid) && $recid->role_id == 4) checked @endif>Visibility
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input userRole" name="userRole" value="5" @if(isset($recid) && $recid->role_id == 5) checked @endif>Field Ops
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input userRole" name="userRole" value="6" @if(isset($recid) && $recid->role_id == 6) checked @endif>Transport Team
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input userRole" name="userRole" value="7" @if(isset($recid) && $recid->role_id == 7) checked @endif>Ad-hoc Staff
                        </label>
                    </div>

                    <input type="hidden" name="role_id" id="roleId" value="@if(isset($recid)) {{$recid->role_id}} @else 0 @endif">
                    
                    @if(isset($recid))
                        <button type="submit" id="updateUsers" class="btn mt-3 bg-teal-400 btn-block">
                            Update <i class="icon-circle-right2 ml-2"></i>
                        </button>
                    @else
                        <button type="submit" id="onBoardUsers" class="btn mt-3 bg-primary-400 btn-block">
                            Onboard <i class="icon-circle-right2 ml-2"></i>
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <!-- Contextual classes -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <h5 class="card-title">Users</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:9px;">
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Phone No</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($users))
                        @foreach($users as $user)
                        <?php $counter++; 
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success'
                        ?>
                        <tr class="{{$css}}" style="font-size:10px">
                            <td>{{$counter}}</td>
                            <td>{{ucwords($user->last_name)}} {{ucwords($user->first_name)}}</td>
                            <td>{{$user->phone_no}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('user-registration/'.$user->id.'/edit')}}" class="list-icons-item text-primary-600">
                                        <i class="icon-pencil"></i>
                                    </a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="table-success">You've not added any user</td>
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
<script src="{{URL::asset('/js/validator/jquery.form.js')}}" type="text/javascript"></script>
<script src="{{URL::asset('/js/validator/auth.js')}}" type="text/javascript"></script>

<script type="text/javascript">
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
@stop