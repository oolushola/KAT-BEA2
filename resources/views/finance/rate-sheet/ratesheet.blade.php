@extends('layout')

@section('title')Kaya ::.Rate Sheet @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Rate Sheet</span></h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">                
              <a href="#newRateSheet" data-target="#newRateSheet" id="newRate" data-toggle="modal" class="btn btn-link btn-float text-default font-weight-semibold"><i class="icon-plus2"></i> <span>Add New</span></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
    &nbsp;

        <!-- Basic layout-->
        <div class="card">
            <div class="card-header header-elements-inline">
              <h5 class="card-title font-size-sm font-weight-bold"> Updated as at {{ date('F, Y')}}</h5>
            </div>
            <div class="card-body">
              <table class="table table-success">
                <thead>
                  <tr>
                    <th>SN</th>
                    <th>Client</th>
                    <th>Tonnage(T)</th>
                    <th>State</th>
                    <th>Destination</th>
                    <th>Client Rate(&#x20A6;)</th>
                    <th>Transporter Rate(&#x20A6;)</th>
                    <th>Margin(&#x20A6;)</th>
                    <th>Edit</th>
                  </tr>
                </thead>
                <tbody>
                  @if(count($ratesheets) > 0)
                    <?php $count = 0; ?>
                    @foreach($ratesheets as $rate)
                      <?php $count++; ?>
                      <tr>
                        <td class="">{{ $count }}</td>
                        <td class="">{{ $rate->company_name }}</td>
                        <td class="">{{ $rate->tonnage/1000 }}T</td>
                        <td class="">{{ $rate->state }}</td>
                        <td class="">{{ $rate->exact_location }}</td>
                        <td class="">{{ number_format($rate->client_rate, 2) }}</td>
                        <td class="">{{ number_format($rate->transporter_rate, 2) }}</td>
                        <td class="">{{ number_format($rate->client_rate - $rate->transporter_rate, 2)}}</td>
                        <td>
                          <i class="icon-pencil updateRate" id="{{$rate->id}}" data-toggle="modal" href="#newRateSheet"></i>
                        </td>
                      </tr>
                    @endforeach
                  @else
                  <tr>
                    <td colspan="10">You have not added any rate.</td>
                  </tr>
                  @endif
                  
                </tbody>
              </table>
            </div>
        </div>
        <!-- /basic layout -->
    </div>
</div>

@include('finance.rate-sheet._ratesheetForm')

@stop

@section('script')
<script type="text/javascript" src="{{URL::asset('js/validator/ratesheet.js')}}"></script>
@stop
