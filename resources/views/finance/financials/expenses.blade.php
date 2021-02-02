@extends('layout')

@section('title')Kaya::.Add Incentives @stop

@section('main')

<div class="content-wrapper">

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Invoice</span> - Invoice Incentive</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="{{URL('invoices')}}" class="btn btn-link btn-float text-default"><i class="icon-folder-search text-primary"></i><span>Invoice Archive</span></a>
                    <a href="{{URL('all-invoiced-trips')}}" class="btn btn-link btn-float text-default"><i class="icon-calculator text-primary"></i> <span>Invoiced</span></a>
                </div>
            </div>
        </div>
        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <a href="{{URL('invoices')}}" class="breadcrumb-item">Invoices</a>
                    <span class="breadcrumb-item active">Incentive</span>
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
                    <form method="POST" id="frmOtherExpenses">
                        @csrf
                        @if(isset($recid))
                            {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}">
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label>Year</label>
                                    <select name="year" id="year" class="form-control">
                                        <option value="0">Choose Year</option>
                                        <?php 
                                            $current_year = date('Y');
                                            for($year_started = 2019; $year_started <= $current_year; $year_started ++) {
                                                if(isset($recid)) {
                                                    if($year_started === $recid->year) {
                                                        echo '<option value="'.$year_started.'" selected>'.$year_started.'</option>';
                                                    }
                                                    else {
                                                        echo '<option value="'.$year_started.'">'.$year_started.'</option>';
                                                    }
                                                }
                                                else{
                                                    echo '<option value="'.$year_started.'" selected>'.$year_started.'</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Month</label>
                                    <select name="month" id="month" class="form-control">
                                        <option value="">Choose Month</option>
                                        <?php 
                                            for($month = 1; $month <= 12; $month++) {
                                               if(isset(($recid)) && ($recid->month == $month)) {
                                                    echo '<option value="'.$month.'" selected>'.date('F', mktime(0,0,0,$month, 1, date('Y'))).'</option>';
                                                }
                                                else {
                                                echo '<option value="'.$month.'">'.date('F', mktime(0,0,0,$month, 1, date('Y'))).'</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <p class="mb-3 font-weight-bold text-right pointer text-success" style="text-decoration:underline" id="addMoreExpensesCategory">Add More</p>

                        <div class="row mb-3 mb-md-2" id="moreExpenses">
                            @foreach($expensesCategories as $expenseCategory)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ $expenseCategory->category }}</label>
                                    <input type="hidden" name="expenses_description[]" value="{{ $expenseCategory->category}} ">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group" style="margin:0; padding:0">
                                    <input type="number" step="0.01" class="form-control" placeholder="Amount" name="amount[]" id="amount" value="@if(isset($recid)){{$expenseCategory->amount}}@endif" style="margin:0; border-radius:0">
                                </div>
                            </div>
                            @endforeach
                            <!-- <section id="moreExpenses"></section> -->
                        </div>

                        

                        <input type="hidden" name="expenses" id="expensesAmount">
                        <div id="responsePlace"></div>

                        <div class="text-right" id="defaultButton">
                            @if(isset($recid))
                            <button type="button" class="btn btn-danger" id="updateExpenses">Update Expenses
                                <i class="icon-coins ml-2"></i>
                            </button>
                            @else
                            <button type="button" class="btn btn-danger" id="addExpenses">Add Expenses
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
                                <th>Period</th>
                                <th>Expenses(&#x20a6;)</th> 
                                <th>Action</th>                         
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($expenses))
                            <?php $count = 0; ?>
                            @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $count += 1 }}</td>
                                    <td>{{ date('F', mktime(0,0,0,$expense->month, 1, date('Y'))) }}, {{ $expense->year }}</td>
                                    <td>{{ number_format($expense->expenses, 2) }}</td>
                                    <td>
                                        <a href="{{URL('other-expenses/'.$expense->id.'/edit')}}">
                                        
                                            <i class="icon-pen"></i>
                                        </a>
                                        <i class="icon-trash"></i>
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
<script type="text/javascript" src="{{URL('/js/validator/expenses.js')}}"></script>
@stop

