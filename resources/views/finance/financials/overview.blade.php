@extends('layout')

@section('title')Financials ::. Dashboard @stop

@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/css/custom.css')}}">
@stop

@section('main')

<!-- Main content -->
    
<div class="content-wrapper">
    <?php
        if(sizeof($monthlyTarget)==0){
            $targetForTheMonth = 150;
        }
        else{
            $targetForTheMonth = $monthlyTarget[0]->target;
        }

        $targetPercentage = round($getGatedOutByMonth / $targetForTheMonth * 100, 2);
        $targetDescription = $targetPercentage.'% of '.$targetForTheMonth.' target';
        function monthListings(){
            $currentMonth = date('F');
            for($i=0; $i<12; $i++){
                $month = date('F', mktime(0,0,0,$i, 1, date('Y')));
                    echo '<option value="'.$month.'">'.$month.'</option>';
            }
        }
        
    ?>

    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Finance</span> - Dashboard</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="{{URL('invoices')}}" class="btn btn-link btn-float text-default">
                        <i class="icon-calculator text-primary"></i> 
                        <span>Invoices</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <span class="breadcrumb-item">Finance </span>
                    <span class="breadcrumb-item active">Dashboard</span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->

    <div class="row">
        <div class="col-md-6 col-sm-6">
            <div class="card mb- mt-1" >
                <canvas id="financeProjection" height="400"></canvas>
            </div>
        </div>

        <div class="col-md-6 col-sm-6">
            <div class="card mb- mt-1" >
                <canvas id="monthlyStatistics" height="400"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 dashboardCard">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker">EXPECTED REVENUE</span>
                        
                        <h1 class="font-weight-semibold text-center dashboardHeading">
                            &#x20a6; {!! number_format($expectedRevenue[0]->expectedrevenue, 2) !!}
                        </h1>
                    </div>

                    <div class="col-md-12 dashboardCard">
                        <table class="table table-condensed">
                        <tbody> 
                                <tr>
                                    <td>Expected Transporter Rate</td>
                                    <td class="condensedChildTwo">&#x20a6;
                                        {!! number_format($expectedCompanyRate[0]->company_rate, 2) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Actual Payments to Transporters</td>
                                    <td class="condensedChildTwo">
                                        &#x20a6; {!! number_format($actualPayments, 2) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Expected Gross Margin</td>
                                    <td class="condensedChildTwo">
                                        <?php
                                            
                                            $expected_revenue = $expectedRevenue[0]->expectedrevenue;
                                            $companyRate = $expectedCompanyRate[0]->company_rate;
                                            $egm = $expected_revenue - $companyRate;
                                            $expectedMargin = ($egm / $expected_revenue) * 100;
                                        ?>
                                        &#x20a6; {!! number_format($egm, 2) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Expected Margin</td>
                                    <td class="condensedChildTwo">{!! round($expectedMargin, 2) !!}%</td>
                                </tr>
                                <tr>
                                    <td>Value of Invoiced </td>
                                    <td class="condensedChildTwo">&#x20a6; {{number_format($totalInvoiced[0]->totalInvoicedAmount, 2)}}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>

                    <div class="col-md-12 dashboardCard__mt15">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker">VALUE OF COMPLETED BUT YET TO BE INVOICED</span>
                        
                        <h1 class="font-weight-semibold text-center dashboardHeading">&#x20a6;
                            {{ number_format($valueofcompletedbutnotinvoiced[0]->completed_value_not_invoiced, 2)}}
                        </h1>
                    </div>

                    <div class="col-md-12 dashboardCard" >
                        <table class="table table-condensed">
                            <tbody> 
                                <tr>
                                    <td>Value of Invoiced but not paid</td>
                                    <td class="condensedChildTwo">&#x20a6; {!! number_format($totalamountofinvoicedbutnotpaid[0]->invoicedNotPaid, 2) !!}</td>
                                </tr>
                                <tr>
                                    <td width="50%">Not Invoiced (Incl. On Journey)</td>
                                    <td class="condensedChildTwo">&#x20a6; {{ number_format($notinvoiced[0]->notInvoiced,2) }}</td>
                                </tr>
                                <tr>
                                    <td>Estimated Balance to Transporters</td>
                                    <td class="condensedChildTwo">
                                        
                                        &#x20a6; {!! number_format($expectedCompanyRate[0]->company_rate - $actualPayments, 2) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Average Revenue Per Trip</td>
                                    <td class="condensedChildTwo">
                                        <?php
                                            $expectedRevenue = $expectedRevenue[0]->expectedrevenue;
                                            $averageRevenuePerTrip = $expectedRevenue / $tripCounts;
                                        ?>
                                        &#x20a6; {!! number_format($averageRevenuePerTrip, 2) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Average Cost Per Trip </td>
                                    <td class="condensedChildTwo">
                                        <?php 
                                            $companyRate = $expectedCompanyRate[0]->company_rate;
                                            $averageCostPerTrip = $companyRate / $tripCounts;
                                        ?>
                                        &#x20a6;{!! number_format($averageCostPerTrip, 2) !!}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>

                    <div class="col-md-12 dashboardCard__mt15">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker">TOP 3 DESTINATION & FREQUENCY</span>
                        <table class="table table-condensed">
                            <tbody> 
                                @if(count($threeDestination))
                                    @foreach($threeDestination as $exactLocation)
                                    <?php $frequency = round($exactLocation->locations / $tripCounts * 100 / 1, 2); ?>
                                    <tr>
                                        <td width="70%">{!! strtoupper($exactLocation->exact_location_id) !!}</td>
                                        <td class="condensedChildTwo">
                                            {!! $exactLocation->locations !!} ({!! $frequency!!}%)
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2">Awaiting Data for Top 3 Loc...</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 dashboardCard">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker" >CARGO AVAILABILITY</span>
                        <h1 class="dashboardHeadingRequest font-weight-semibold text-center">
                            {!! $availableCargo[0]->total_order !!}
                        </h1>
                    </div>
                    <div class="col-md-12 dashboardCard">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker" >GATED OUT (TOTAL)</span>
                        <h1 class="font-weight-semibold text-center dashboardHeadingRequest">{!! $tripCounts !!}</h1>
                    </div>

                    <div class="col-md-12 " id="dashboardRequestBreakDown">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker">BREAKDOWN</span>
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td width="70%" >Completed but not Invoiced</td>
                                    <td class="condensedChildTwo">{{$offloadedButNotInvoiced[0]->completed_not_invoiced}}</td>
                                </tr>
                                <tr>
                                    <td width="70%">Offloaded Trips</td>
                                    <td class="condensedChildTwo">{!! $offloadedTrips !!}</td>
                                </tr>
                                <tr>
                                    <td width="70%">Invoiced</td>
                                    <td class="condensedChildTwo">{!! $numberofinvoiced[0]->totalInvoiced !!}</td>
                                </tr>
                                <tr>
                                    <td>On Journey</td>
                                    <td class="condensedChildTwo">{!! $onJourney !!}</td>
                                </tr>
                                <tr>
                                    <td>At Destination</td>
                                    <td class="condensedChildTwo">{!! $atDestination !!}</td>
                                </tr>
                                <tr>
                                    <td>Tonnage (Total)</td>
                                    <td class="condensedChildTwo">{!! $totalTons[0]->tons_in_total/1000 !!} <sub>t</sub></td>
                                </tr>
                                <tr>
                                    <td>Gated Out (Today)</td>
                                    <td class="condensedChildTwo">{!! $numberofdailygatedout !!}</td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>

                    <div class="col-md-12 dashboardCard__mt15">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker">TOP 3 LOADING SITES & FREQUENCY</span>
                        <table class="table table-condensed">
                            <tbody> 
                                @if(count($threeLoadingSites))
                                    @foreach($threeLoadingSites as $loadingSites)
                                    <?php $frequency = round($loadingSites->sites / $tripCounts * 100 / 1, 2); ?>
                                    <tr>
                                        <td width="70%">{!! $loadingSites->loading_site !!}</td>
                                        <td class="condensedChildTwo">
                                            {!! $loadingSites->sites !!} ({!! $frequency!!}%)
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2">Awaiting Data for Top 3 Loading Sites...</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 dashboardCard">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker">GATED OUT FOR THE MONTH OF 
                            <select>
                                <?php
                                $currentMonth = date('F');
                                for($m=1; $m<=12; $m++) {
                                    $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                                    if($currentMonth == $month) {
                                    echo "<option value=".$month." selected>".$month."</option>";
                                    } else {
                                    echo "<option value=".$month.">".$month."</option>";
                                    }
                                }
                                ?>
                            </select>
                            {!! date('Y') !!}
                        </span>
                        
                        <h1 class="dashboardHeadingRequest font-weight-semibold text-center">
                            {!! $getGatedOutByMonth !!}
                        </h1>
                    </div>

                    <div class="col-md-12 dashboardCard__mt15">
                        <span class="badge bg-danger-400 font-weight-semibold" id="target">
                            {!! $targetDescription !!}
                        </span>

                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th colspan="2" >Quick Search 
                                        <select id="quickSearch">
                                        @foreach($allTrips as $trip)
                                        <option value="{{$trip->id}}">{{$trip->trip_id}}</option>
                                        @endforeach
                                        </select>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $currentStage = $currentTrip[0]->tracker;
                                    if($currentStage == 1){ $current_stage = 'GATED IN';}
                                    if($currentStage == 2){ $current_stage = 'ARRIVAL AT LOADING BAY';}
                                    if($currentStage == 3){ $current_stage = 'LOADING';}
                                    if($currentStage == 4){ $current_stage = 'DEPARTURE';}
                                    if($currentStage == 5){ $current_stage = 'GATED OUT';}
                                    if($currentStage == 6){ $current_stage = 'ON JOURNEY';}
                                    if($currentStage == 7){ $current_stage = 'ARRIVED DESTINATION';}
                                    if($currentStage == 8){ $current_stage = 'OFFLOADED';}

                                    $balancePaid = $currentTrip[0]->balance_paid;
                                    $advancePaid = $currentTrip[0]->advance_paid;
                                    if($advancePaid == true AND $balancePaid == false) {$payment = 'Advance Paid';}
                                    if($advancePaid == true and $balancePaid == true) {$payment = 'Paid';}
                                    if($advancePaid == false and $balancePaid == false) {$payment = 'Not Paid';}
                                ?>
                                
                                <tr>
                                    <td width="70%">Waybill No</td>
                                    <td class="condensedChildTwo">
                                        @foreach($tripWaybills as $waybill)
                                            {!! $waybill->invoice_no !!}<br>
                                        @endforeach
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td width="70%">Vehicle No</td>
                                    <td class="condensedChildTwo">{!! $currentTrip[0]->truck_no !!}</td>
                                </tr>
                                <tr>
                                    <td width="70%">Destination</td>
                                    <td class="condensedChildTwo">{!! $currentTrip[0]->exact_location_id !!}</td>
                                </tr>
                                <tr>
                                    <td width="70%">Product</td>
                                    <td class="condensedChildTwo">{!! $currentTrip[0]->product !!}</td>
                                </tr>
                                <tr>
                                    <td width="70%">Sales Order No</td>
                                    <td class="condensedChildTwo">
                                        @foreach($tripWaybills as $waybill)
                                            {!! $waybill->sales_order_no !!}<br>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td width="70%">Current Stage</td>
                                    <td class="condensedChildTwo">{!! $current_stage !!}</td>
                                </tr>
                                <tr>
                                    <td width="70%">Invoice Status</td>
                                    <td class="condensedChildTwo">
                                        @foreach($tripWaybills as $waybill)
                                            @if($waybill->invoice_status==false) {{'Not Invoiced'}} @else {{'Invoiced'}}
                                            @endif
                                            @break
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td width="70%">Payment Status</td>
                                    <td class="condensedChildTwo">{!! $payment !!}</td>
                                </tr>
                                
                                
                            </tbody>
                        </table>
                        
                    </div>

                    <div class="col-md-12 dashboardCard__mt15">
                        <span class="bg-danger-400 font-weight-semibold dashboardMarker">TOP 3 PRODUCTS & FREQUENCY</span>
                        <table class="table table-condensed">
                            <tbody> 
                                @if(count($topThreeProducts))
                                    @foreach($topThreeProducts as $productList)
                                    <?php $frequency = round($productList->products / $tripCounts * 100 / 1, 2); ?>
                                    <tr>
                                        <td width="70%">{!! strtoupper($productList->product) !!}</td>
                                        <td class="condensedChildTwo">
                                            {!! $productList->products !!} ({!! $frequency!!}%)
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2">Awaiting Data for Top 3 Products...</td>
                                    </tr>
                                @endif
                                
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <?php
        $availableCargoStatistics = [$availableCargo[0]->total_order, $tripCounts];

        $expectedRevenue = $expected_revenue;
        $transporterRate = $expectedCompanyRate[0]->company_rate;
        $paymentToTransporters = $actualPayments;
        $expectedGrossMargin = $egm;
        $valueofInvoiced = $totalInvoiced[0]->totalInvoicedAmount;
        $completedTripsButNotInvoiced = $valueofcompletedbutnotinvoiced[0]->completed_value_not_invoiced;

        $financeProjections = array(
            $expectedRevenue, $transporterRate, $paymentToTransporters, $expectedGrossMargin, $valueofInvoiced, $completedTripsButNotInvoiced
        );

        
    ?>

    <input type="hidden" value="{{date('F, Y')}}" id="currentMonthInTheYear">

</div>
<!-- /main content -->

@stop


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>

<!-- Finance projections -->
<script>
    var ctx = document.getElementById('financeProjection');
    var financeProjectionArray = <?php echo json_encode($financeProjections); ?>;
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Expected Revenue', 'Expected Transporter Rate', 'Payments to Transporters', 'Expected Gross Margin', 'Value of Invoiced', 'Completed, Not Invoiced'],
            datasets: [{
                label: 'Finance Projections',
                data: financeProjectionArray,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>

<!-- Monthly Statistics of the year -->
<script>
    var ctx = document.getElementById('monthlyStatistics');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Gated Out Monthly Statics',
                data: [200, 300, 800, 749, 900, 300, 309, 408, 1000, 1200, 1150, 2800],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            legend:{
                labels:{
                    fontColor:'#333',
                    fontFamily: 'tahoma'
                }
            }
        }
    });
</script>
@stop