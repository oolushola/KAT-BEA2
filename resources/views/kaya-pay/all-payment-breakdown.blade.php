@extends('layout')

@section('title') Kaya Pay::.All Payment Request @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
.filterStyle { border: 1px solid #ccc; padding: 5px; font-size: 10px; margin: 0px 3px; width: 120px; outline:none }
</style>
@stop
@section('main')

<div class="card">
    <div class="row ml-2 mr-2 mt-1">
      <div class="col-md-2">
        <div class="card hover filter" data-id="showClients">
          <div class="card-body font-weight-bold">CLIENT</div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card hover filter" data-id="show">
          <div class="card-body font-weight-bold" id="shootByVoidedTrips">PAYMENT STATUS</div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card hover filter" data-id="show">
          <div class="card-body font-weight-bold" id="shootByVoidedTrips">DELETED TRIPS</div>
        </div>
      </div>
      <div class="col-md-2">
      <input type="text" class="form-control mt-2" placeholder="Search Record" id="searchDataset" />
        <div class="card-body font-weight-bold"></div>
      </div>
    </div>    
    
    <div class="table-responsive mt-1" id="contentPlaceholder">
      <table class="table table-bordered">
        <thead class="table-info" style="font-size:11px; background:#000; color:#eee;">
          <tr class="font-weigth-semibold">
            <th class="headcol">KAYA PAY ID</th>
            <th>CLIENT INFO</td>
            <th>WAYBILL NO.</th>
            <th class="text-center">TRUCK NO.</th>
            <th class="text-center bg-danger-400">DISBURSED</th>
            <th class="text-center">DUE DATE</th>
            <th class="text-center">DUE</th>
            <th class="text-center">OVERDUE CHARGE</th>
            <th class="text-center">FINANCE COST</th>
            <th class="text-center">FINANCE INCOME</th>
            <th class="text-center">NET MARGIN</th>
            <th class="text-center">AMOUNT RECEIVEABLE</th>
          </tr>
        </thead>
        <tbody id="masterDataTable">
            <?php $counter = 0; ?>
            @if(count($paymentBreakdownListings))
                @foreach($paymentBreakdownListings as $trip)
                <?php 
                  $counter++;
                  $counter % 2 == 0 ? $css = ' font-weight-semibold ' : $css = 'order-table font-weight-semibold';
                  $now = time();
                  $due_date = strtotime($trip->valid_until);;
                  $datediff = $due_date - $now;
                  $numberofdays = round($datediff / (60 * 60 * 24));
                  if($numberofdays < 0 && !$trip->payment_status) {
                    $expired = -1 * $numberofdays;
                    $response = $expired.' Days ago ';
                    $bgcolor = "red";
                    $color = "#fff";
                    $overdueCharged = $expired * $trip->overdue_charge;
                  }
                  else if($numberofdays < 0 && $trip->payment_status) {
                    $expired = -1 * $numberofdays;
                    $response = date("d-m-Y", strtotime($trip->date_paid));
                    $bgcolor = "";
                    $color = "";

                    $daysDefaulted = strtotime($trip->valid_until) - strtotime($trip->date_paid);
                    $defaultedFor = abs(round($daysDefaulted / (60 * 60 * 24)));

                    $overdueCharged = $defaultedFor * $trip->overdue_charge;
                    
                  }
                  else{
                    $response = 'In '.$numberofdays.' days';
                    $bgcolor = "green";
                    $color = "#fff";
                    $overdueCharged = 0;
                  }
                  
                ?>                        
                <tr class="{{$css}} hover" style="font-size:10px;">
                    <td class="text-center">
                      <a href="#" class="list-icons-item text-primary-600" title="Update this trip">
                        {{$trip->kaya_pay_id}}</a><br />
                        {{ date('d-m-Y', strtotime($trip->gated_out)) }}
                    </td>
                    <td>{{ strtoupper($trip->client) }}, {{$trip->loading_site}} <br />
                        <span class="badge badge-info">{{strtoupper($trip->destination_state) }}, {{ strtoupper($trip->destination_city) }}</span>
                    </td>
                    <td class="font-weight-semibold">{{ $trip->waybill_no }}</td>
                    <td class="text-center">
                      {{strtoupper($trip->truck_no)}}
                    </td>
                    <td class="text-center bg-danger-400">
                      {{ date('d-m-Y', strtotime($trip->payment_disbursed)) }}
                    </td>
                    <td class="text-center font-weight-bold" style="background:{{$bgcolor}}; color:{{$color}}">{{ date('d-m-Y', strtotime($trip->valid_until)) }}</td>
                    <td class="text-center font-weight-bold" style="background:{{$bgcolor}}; color:{{$color}}">
                      {{ $response }}
                      @if($trip->payment_status)
                        <span class="icon-checkmark-circle2 ml-1"></span>
                      @else
                        <span class="icon-cancel-circle2 ml-1"></span>
                      @endif
                    </td>
                    <td class="text-center">&#x20A6;{{ number_format($overdueCharged, 2) }}</td>
                    <td class="text-center">&#x20A6;{{ number_format($trip->finance_cost, 2) }}</td>
                    <td class="text-center">&#x20A6;{{ number_format($trip->finance_income, 2) }}</td>
                    <td class="text-center">&#x20A6;{{ number_format($trip->net_income, 2) }}</td>
                    <td class="text-center">&#x20A6;{{ number_format($trip->net_income + $overdueCharged, 2) }}</td>
                </tr>
                @endforeach
            @else   
                <tr>
                    <td class="table-success" colspan="30">No breakdown recorded yet.</td>
                </tr>
            @endif            

        </tbody>
      </table>
    </div>
</div>

@stop

@section('script')

<script>
    $(window).scroll(function() {
        if($(window).scrollTop() == $(document).height() - $(window).height()) {
            // ajax call get data from server and append to the div
        }
    });
</script>
@stop