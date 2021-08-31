@extends('layout')

@section('title')Kaya::.Departments @stop

@section('main')

<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Preference</span> - Departments</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-link btn-float text-default"><span></span></a>
                </div>
            </div>
        </div>
        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i>Home</a>
                    <a href="#" class="breadcrumb-item">Preference</a>
                    <span class="breadcrumb-item active">Department</span>
                </div>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <div class="row">
        <div class="col-md-5">
            &nbsp;
            <!-- Basic layout-->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h5 class="card-title">Department </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="frmDepartment">
                        @csrf
                        @if(isset($recid))
                            {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                        @endif

                        <div class="row"> 
                          <div class="col-md-12">
                            <div class="form-group">
                                <label>Head of Department</label>
                                <select type="text" class="form-control" name="head_of_department" id="headOfDepartment">
                                  <option value="">Choose Head of Department</option>
                                  @foreach($users as $user)
                                    @if(isset($recid) && $recid->head_of_department === $user->id)
                                    <option value="{{$user->id}}" selected>
                                      {{ucwords($user->first_name)}} 
                                      {{ ucwords($user->last_name)}}
                                    </option>
                                    @else
                                    <option value="{{$user->id}}">
                                      {{ucwords($user->first_name)}} 
                                      {{ ucwords($user->last_name)}}
                                    </option>
                                    @endif
                                  @endforeach
                                </select>
                            </div>
                          </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                <label>Department</label>
                                <input type="text" class="form-control" name="department" id="department" value="@if(isset($recid)){{$recid->department}}@endif" />
                              </div>
                          </div>
                        </div>
                        <div id="loader"></div>

                        <div class="text-right" id="defaultButton">
                            @if(isset($recid))
                            <button type="button" class="btn btn-danger font-weight-bold" id="updateDepartment">Update Department
                            </button>
                            @else
                            <button type="button" class="btn btn-danger font-weight-bold" id="addDepartment">Add Department
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

          <!-- Contextual classes -->
          <div class="card">
              <div class="card-header header-elements-inline">
                <h5 class="card-title">Preview Pane</h5>
              </div>

              <div class="table-responsive" id="contentHolder">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Head of Department</th>
                            <th>Department</th>
                            <th>Action</th>                         
                        </tr>
                    </thead>
                    <tbody>
                      @if(count($departments))
                      <?php $count = 0; ?>
                      @foreach($departments as $department)
                        <tr>
                          <td>{{ $count+= 1  }}</td>
                          <td>{{ ucwords($department->first_name)}} 
                            {{ ucwords($department->last_name) }}</td>
                          <td>{{ $department->department }}</td>
                          <td>
                            <a href="{{URL('department/'.$department->id.'/edit')}}"><i class="icon-pencil"></i></a>
                          </td>
                        </tr>
                      @endforeach
                      @else
                      <tr>
                        <td>No department has been added yet.</td>
                      </tr>
                      @endif
                    </tbody>
                
                </table>
              </div>
          </div>
          <!-- /contextual classes -->


        </div>
    </div>

</div>

@stop

@section('script')
<script type="text/javascript" src="{{URL('/js/validator/department.js?v=').time()}}"></script>
@stop

