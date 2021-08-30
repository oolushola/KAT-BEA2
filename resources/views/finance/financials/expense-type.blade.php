@extends('layout')

@section('title')Kaya::.Expense Category @stop

@section('main')

<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Expense</span> - Expense Type</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href=".opex" data-toggle="modal"  class="btn btn-link btn-float text-default showOpex"><i class="icon-coins text-primary"></i><span>Opex</span></a>
                    <a href="{{URL('all-invoiced-trips')}}" class="btn btn-link btn-float text-default"><i class="icon-git-branch text-primary"></i> <span>Pair Expense to Department</span></a>
                </div>
            </div>
        </div>
        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="{{URL('invoices')}}" class="breadcrumb-item">Expense</a>
                    <span class="breadcrumb-item active">Expense Type</span>
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
                    <h5 class="card-title">Expenses </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="frmExpenseType">
                        @csrf
                        @if(isset($recid))
                            {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                        @endif

                        <div class="row"> 
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label>Expense Type</label>
                                  <input type="text" class="form-control" name="expense_type" id="expenseType" />
                              </div>
                          </div>
                        </div>
                        <div id="loader"></div>

                        <div class="text-right" id="defaultButton">
                            @if(isset($recid))
                            <button type="button" class="btn btn-danger" id="updateExpenseType">Update Expense Type
                                <i class="icon-coins ml-2"></i>
                            </button>
                            @else
                            <button type="button" class="btn btn-danger" id="addExpenseType">Add Expense Type
                                <i class="icon-coins ml-2"></i>
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
                                <th>Expense Type</th>
                                <th>Action</th>                         
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($expenseTypes))
                            <?php $count = 0; ?>
                            @foreach($expenseTypes as $expenseType)
                                <tr>
                                    <td>{{ $count += 1 }}</td>
                                    <td>{{ $expenseType->expense_type }}</td>
                                    <td>
                                        <a href="{{URL('expense-type/'.$expenseType->id.'/edit')}}">
                                        
                                            <i class="icon-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                                <tr>
                                    <td>No expenses has been added yet.</td>
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
<script type="text/javascript" src="{{URL('/js/validator/expense-type.js?v=').time()}}"></script>
@stop

