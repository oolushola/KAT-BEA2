<form id="frmAssignDepartmentExpense" method="POST">
    @csrf
    <div id="assignDepartmentExpense" class="modal fade" >
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header" style="padding:5px; background:#fbfbfb">
                    <h5 class="font-weight-sm font-weight-bold text-warning">
                        <span id="selectedStatus">Assign Expense Type to Department
                        <select style="outline:none; padding:10px; font-size:12px; border: 1px solid #ccc" name="department_id" id="department">
                            <option value="0">Choose department</option>
                            @foreach($departments as $department)
                                <option value="{{$department->id}}">{{$department->department}}</option>
                            @endforeach
                        </select>
                        </span>
                    </h5>
                    <button type="button" class="close font-weight-bold text-danger" data-dismiss="modal" style="position:relative; top:20px; padding:0px; right:20px; padding:5px;">CLOSE &times;</button>
                </div>
                
                <div class="modal-body"> 
                    <input type="hidden" value="0" id="validator">
                    <p class="m-0 mt-2" id='loader2'></p>
                    <div id="contentDropper">
                        <div class="row">
                            <div class="col-md-5">
                                &nbsp;
                                <div class="card" >
                                    <div class="table-responsive" style="max-height:1050px">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td class="table-primary font-weight-bold font-size-sm" colspan="5">Expense Type</td>
                                                </tr>
                                                <tr>
                                                    <td class="table-info" width="10%">
                                                        <input type="checkbox" id="selectAllLeft">
                                                    </td>
                                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllLeftText">
                                                        Select all expense types
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size:10px;" class="assignExpenseType">
                                            @if(count($expenseTypes))
                                                <?php $count = 0; ?>
                                                @foreach($expenseTypes as $key => $expenseType)

                                                <?php $count++; if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } ?>
                                                    <tr class="{{ $cssStyle }}">
                                                        <td>
                                                            <input type="checkbox" value="{{ $expenseType->id }}" class="availableExpenseType" name="expenseTypes[]" />
                                                        </td>
                                                        <td>{{ $expenseType->expense_type }}</td>
                                                    </tr>
                                                @endforeach
                                                @else
                                                    <tr class="table-success" style="font-size:10px">
                                                        <td colspan="2" class="font-weight-semibold">You do not have any expense type yet</td>
                                                    </tr>
                                                @endif
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                &nbsp;
                                <div class="text-center mt-5">
                                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="assignExpenseType">ASSIGN
                                        <i class="icon-point-right ml-2"></i>
                                    </button>
                                    <br /><br />
                                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeExpenseType">REMOVE <i class="icon-point-left ml-2"></i></button>
                                </div>
                            </div>

                            <div class="col-md-5">
                                &nbsp;
                                <!-- Contextual classes -->
                                <div class="card">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td class="table-primary font-weight-bold font-size-sm" colspan="4">Assigned Expense Type</td>
                                                </tr>
                                                <tr>
                                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all assigned expense type</td>
                                                </tr>
                                            </thead>
                                            <tbody style="font-size:10px;">
                                                <tr class="table-success" style="font-size:10px">
                                                    <td colspan="2" class="font-weight-semibold">You've not assigned any expense type yet.</td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /contextual classes -->


                            </div>

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</form>


