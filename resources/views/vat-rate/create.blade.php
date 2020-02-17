@extends('layout')

@section('title')Kaya ::. Vat Rate @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home - Finance</span> - Vat % </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default"><i class="icon-coins text-primary"></i> <span>Vat Rate</span></a>
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
                <h5 class="card-title">
                    @if(isset($recid)) Update @else Add @endif Vat %
                </h5>
            </div>

            <div class="card-body">
                <form action="" name="frmVatRate" id="frmVatRate">
                    @csrf
                    @if(isset($recid)) {!! method_field('PATCH') !!} <input type="hidden" name="id" id="id" value="{{$recid->id}}"> @endif

                    <div class="form-group">
                        <label>Withholding Tax(%)</label>
                        <input type="number" class="form-control" placeholder="Withholding Tax" name="withholding_tax" id="withholdingtax" value="{{ $vatRateRecord->withholding_tax }}" />
                    </div>

                    <div class="form-group">
                        <label>Vat Rate(%)</label>
                        <input type="number" class="form-control" placeholder="V.A.T" name="vat_rate" id="vat_rate" value="{{ $vatRateRecord->vat_rate }}">
                    </div>

                    <div class="text-right">
                        <span id="loader"></span>
                            <button type="submit" class="btn btn-primary" id="addVatRate" >@if($vatRateRecord->vat_rate != '') Update @else Save @endif Vat Rate 
                        
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
                <h5 class="card-title">Preview Pane of Vat %</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-info">
                        <tr style="font-size:11px;">
                            <th>#</th>
                            <th>Withholding Tax</th>
                            <th>Tax Rate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 0; ?>
                    @if(count($vatRates))
                    @foreach($vatRates as $vatRate)
                    <?php
                        $counter++;
                        $counter % 2 == 0 ? $css = '' : $css = 'table-success';
                    ?>
                    <tr class="{{$css}}" style="font-size:10px">
                        <td>{{$counter}}</td>
                        <td>{{strtoupper($vatRate->withholding_tax)}}</td>
                        <td>{{$vatRate->vat_rate}}</td>
                        <td>
                            <div class="list-icons">
                                <a href="#" class="list-icons-item text-danger-600"><i class="icon-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    @else
                    <tr class="table-info">
                        <td colspan="6"></td>
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
<script type="text/javascript" src="{{URL::asset('js/validator/vat-rate.js')}}"></script>
@stop
