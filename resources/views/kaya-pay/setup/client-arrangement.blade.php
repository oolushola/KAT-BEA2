@extends('layout')

@section('title')Kaya Pay ::. Agreements @stop
@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
</style>

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Kaya Pay</span> - Agreements</h4>
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
                <h5 class="card-title">@if(isset($recid))Update @else Add @endif Agreement</h5>
            </div>

            <div class="card-body">
                <form action="" id="frmAgreement" name="frmAgreement">
                    @csrf
                    @if(isset($recid))
                    <input type="hidden" name="id" id="id" value="{{$recid->id}}" />
                    {!! method_field('PATCH') !!}
                    @endif
                    
                    <div class="form-group">
                        <label>Client</label>
                        <select class="form-control" name="client_id" id="clientId">
                          <option value="">Choose a client</option>
                          @foreach($clients as $client)
                            @if(isset($recid) && $recid->client_id == $client->id)
                            <option value="{{$client->id}}" selected>{{$client->company_name}}</option>
                            @else
                            <option value="{{$client->id}}">{{$client->company_name}}</option>
                            @endif
                          @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Payback In (No of Days)</label>
                        <input type="number" class="form-control" name="payback_in" id="paybackIn" value="<?php if(isset($recid)){ echo $recid->payback_in; } ?>" />
                    </div>

                    <div class="form-group">
                        <label>Interest Rate(%)</label>
                        <input type="number" class="form-control" name="interest_rate" id="interestRate" value="<?php if(isset($recid)){ echo $recid->interest_rate; } ?>" />
                    </div>
                    <div class="form-group">
                        <label>Overdue Charge (&#x20A6;)</label>
                        <input type="number" class="form-control" name="overdue_charge" id="overdueCharge" value="<?php if(isset($recid)){ echo $recid->overdue_charge; } ?>" />
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                        @if(isset($recid))
                            <button type="submit" class="btn btn-primary" id="updateAgreement">Update Agreement 
                        @else
                            <button type="submit" class="btn btn-primary" id="addAgreement">Add Agreement 
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
                <h5 class="card-title">Agreements</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Client</th>
                            <th class="text-center">Payback In</th>
                            <th class="text-center">Interest Rate (%)</th>
                            <th class="text-center">Overdue Charge (&#x20A6;)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($clientArrangements))
                        @foreach($clientArrangements as $agreement)
                        <?php $counter++;
                            $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                        ?>
                        <tr class="{{$css}}" style="font-size:10px">
                            <td>{{$counter}}</td>
                            <td width="20%S">{{$agreement->company_name}}</td>
                            <td class="text-center">{{$agreement->payback_in}}</td>
                            <td class="text-center">{{$agreement->interest_rate}}</td>
                            <td class="text-center">{{number_format($agreement->overdue_charge, 2)}}</td>
                            <td>
                                <div class="list-icons">
                                    <a href="{{URL('kaya-pay-agreements/'.$agreement->id.'/edit')}}" class="list-icons-item text-primary-600"><i class="icon-pencil7"></i></a>
                                    <a href="#" class="list-icons-item text-danger-600">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else

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
<script type="text/javascript" src="{{URL::asset('js/validator/client-agreement.js?v=').time()}}"></script>
@stop
