
@if(!isset(Auth::user()->email))
<script>window.location.href="/"</script>

@elseif(Auth::user()->role_id == 5)
<script>window.location.href='/update-trip'</script>


@elseif(Auth::user()->role_id == 7)
<script>window.location.href='/offloading/my-trips-view'</script>
    
@elseif(Auth::user()->email)
@extends('layout')
@section('title') Welcome, {{ucfirst(Auth::user()->first_name)}} {{ucfirst(Auth::user()->last_name)}} @stop

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
<link rel="stylesheet" type="text/css" href="{{URL::asset('/css/custom.css')}}">
<style>
input, select{
    outline:none
}
.waybillStatus .table td, .table.th{
    padding:3px;
    font-weight:bold;
    
}
</style>
@stop

@section('main')

<!-- Main content -->
@include('_partials.gate-out-month-view')
@include('_partials.current-gate-out-view')
@include('_partials.current_trip_status')
@include('_partials.truck-availability')
@include('_partials.waybill_report')
@include('_partials.specific_date_range_trip')

<div class="content-wrapper">
    <?php
        function monthListings(){
            $currentMonth = date('m');
            for($i=1; $i<=12; $i++){
                $month = date('F', mktime(0,0,0,$i, 1, date('Y')));
                    if($currentMonth == $i) {
                        echo '<option value="'.$i.'" selected>'.$month.'</option>';
                    }
                    else {
                        echo '<option value="'.$i.'">'.$month.'</option>';
                    }
            }
        }

        function waybillByCategory($yetTobeInvoicedWaybill) {
            $counter = 1;
            $healthy = 0;
            $warning = 0;
            $danger = 0;
            foreach($yetTobeInvoicedWaybill as $object){
                
                $now = time();
                $gatedOut = strtotime($object->gated_out);;
                $datediff = $gatedOut - $now;
                $numberofdays = (floor($datediff / (60 * 60 * 24)) * -1) -1;
                

                if($numberofdays >=0 && $numberofdays <= 3  ){
                    $healthy += 1;
                }
                else if(($numberofdays > 3) && ($numberofdays <=7)){
                    $warning += 1;
                }
                else{
                    $danger += 1;
                }
            }
            return $waybillStatusCount = array($healthy, $warning, $danger);
        }
        
    ?>


    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home</span> - Dashboard</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-danger font-weight-bold">{{$totalGateOuts}}</h2>
                        <p class="text-primary font-weight-bold">TOTAL GATE OUT</p>
                    </a>
                    <a href="#truckAvailability" data-toggle="modal"  class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-primary font-weight-bold">{{ count($availableTrucks) }}</h2>
                        <p class="text-primary font-weight-bold">TRUCK AVAILABILITY</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                    <span class="breadcrumb-item active">Dashboard</span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="breadcrumb justify-content-center mr-3">
                    <a href="{{URL('view-trip-thread')}}" class="breadcrumb-elements-item">
                        <i class="icon-eye mr-2"></i>
                        View Trip Thread
                    </a>
                </div>
                <div class="breadcrumb justify-content-center text-danger">
                    <a href="#" class="breadcrumb-elements-item">
                        <i class="icon-flag4 mr-1 text-danger"></i>
                        Flagged Trips
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- /page header -->

    <div class="row">
        <div class="col-md-4 col-sm-12 mb-2">
            <div class="dashboardbox">
                <table class="table">
                    <tr>
                        <td class="font-size-xs font-weight-bold text-center">
                            <i class="icon-trophy3 text-warning-400 text-center" style="font-size:22px"></i>
                            <span class="d-block">Current Month Target</span>
                        </td>
                        <td colspan="2" class="font-weight-semibold" id="target-label">
                            <select class="dashboard-select" style="width:55px" id="currentYear">
                                <?php 
                                    for($yearStarted = 2019; $yearStarted <= date('Y'); $yearStarted++) {
                                        if($yearStarted == date('Y')) {
                                            echo '<option value="'.$yearStarted.'" selected>'.$yearStarted.'</option>';
                                        } else {
                                        echo '<option value="'.$yearStarted.'">'.$yearStarted.'</option>';
                                        }
                                    }
                                ?>
                            </select>
                            <select class="dashboard-select" id="previousMonthTarget" name="previous_month_target" style="width:60px">
                                <option>Previous Months</option>
                                {!! monthListings() !!}
                            </select>
                        </td>
                    </tr>
                </table>
                <hr class="mt-5-negative">
                <canvas id="targetProcessChart" height="200" data-toggle="modal" href="#totalGateOutForTheMonth"></canvas>
                <?php 
                    if(!$monthlyTarget){
                        $targetforthemonth = 200;
                    } else{
                        $targetforthemonth = $monthlyTarget->target;
                    }
                    $gatedOutForTheMonth = $getGatedOutByMonth;
                    $gatedOutPercentageRate = round($gatedOutForTheMonth/$targetforthemonth * 100, 1);
                ?>

                <p id="target-value">
                    <span class="text-primary-400" id="gateOutMonthPlaceholder">{{$gatedOutForTheMonth}}</span> of 
                    <span class="text-danger-400" id="targetPlaceholder">{{$targetforthemonth}}</span>
                </p> 

                <span id="target-percentage">
                    <sub id="target-percentage__value" class="percentageHolder">{{$gatedOutPercentageRate}}% 0f {{$targetforthemonth}}</sub>
                </span>
            </div>
        </div>

        <div class="col-md-8 col-sm-12 mb-2" data-toggle="modal" href="#currentTripStatus">
            <div class="dashboardbox">
                <table class="table">
                    <tr>
                        <td><i class="icon-truck text-primary-400 icon"></i></td>
                        <td id="trip-label" class="font-weight-semibold">TRIP 
                            <span style="float:right">
                                <select class="dashboard-select" id="clientTripStatus">
                                    <option>All Status</option>
                                    @foreach($allclients as $client)
                                    <option value="{{$client->id}}">{{ucwords($client->company_name)}}</option>
                                    @endforeach
                                </select>
                            </span>
                        </td>
                        
                    </tr>
                </table>
                <hr class="mt-5-negative">
                <canvas id="masterTripChart" height="130">
                    <span>Gate In</span>
                    <span>At Loading Bay</span>
                    <span>Departed Loading Bay</span>
                    <span>On Journey</span>
                    <span>At Destination</span>
                    <span>Offloaded</span>
                </canvas>
                
                    <?php
                        $offloadedTrips = $offloadedTrips[0]->offloadedTrips;
                        $stagesOfOperation = [$gateIn, $loadingBay, $departedLoadingBay, $onJourney, $atDestination, $offloadedTrips];

                    ?>
            </div>
            
        </div>

        
    </div>


    <!-- Waybill and Daily line chart of Operation -->
    <div class="row mt-3">
        <div class="col-md-4 mb-2" data-toggle="modal" href="#currentGateOutInformation">
            <div class="dashboardbox">
                <canvas id="myProjectionChart"  height="200"></canvas>
            </div>
        </div>
        <div class="col-md-8 col-sm-12 mb-2" id="">
            <div class="dashboardbox">
                <canvas id="dailyGateOutChart" height="130"></canvas>
            </div>
        </div>

        
    </div>


    <div class="row mt-3">
        <div class="col-md-4 mb-2">
            <div class="dashboardbox" data-toggle="modal" href="#waybillReportStatus">
                <canvas id="waybillChart"  height="200"></canvas>
            </div>
            <?php $waybillCategories = waybillByCategory($tripWaybillYetToReceive); ?>
        </div>

        <div class="col-md-4 mb-2" id="placeholderForWeek">
            <div class="dashboardbox">
                <input type="hidden" id="currentWeekInView" value="{{date('Y/m/d', strtotime('last sunday'))}}">
                <input type="hidden" id="presentDay" value="{{date('Y/m/d')}}">
                <input type="hidden" id="gatedOutForTheWeekValue" value="{{$noOfGatedOutTripForCurrentWeek[0]->weeklygateout}}">


                <canvas id="gatedOutForTheWeek" class="chartOfDateRange" height="200" data-toggle="modal" href="#specificDateRangeInformation"></canvas>
                
                <input class="font-weight-semibold" type="date" style="margin-top:10px;" name="week_one" id="weekOne" value="{{date('Y-m-d', strtotime('last sunday'))}}">
                <input  class="font-weight-semibold" type="date" name="week_two" id="weekTwo" value="{{date('Y-m-d')}}">
                <span><button style="font-size:10px;" class="font-weight-semibold chartOfDateRange">GO</button></span>
                <span class="d-block font-size-sm" id="dateRangeLoader"></span>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="dashboardbox">
                <canvas id="gatedOutForTheMonth" height="200"></canvas>

                <span style="margin:0; padding:0px; font-size:10px; font-family:tahoma">Compare Two Months</span>
                <select name="firstMonthComparator" id="firstMonthComparator" class="dashboard-select">
                    <option value="0">Choose Initial Month</option>
                    {!! monthListings() !!}
                </select>
                
                <select name="secondMonthComparator" id="secondMonthComparator" class="dashboard-select">
                    <option value="0">Choose Second Month</option>
                    {!! monthListings() !!}
                </select>

                <button id="compareTheTwoMonths" style="font-size:10px;" class="font-weight-semibold">Compare</button>

            </div>
        </div>
    </div>

    <section class="row mt-3 ml-3" id="visualize">
        <span>Visualize loading site operation data by: 
            <input type="checkbox" name="dataVisualization" id="monthDv" class="ml-2"> Month
            <input type="checkbox" name="dataVisualization" id="weekRangeDv" class="ml-2"> Week Range
            <input type="checkbox" name="dataVisualization" id="dayDv" class="ml-2"> Specific Day 
        </span>
        <div class="ml-3 hidden" id="monthPlaceHolder">
                <div id="monthPlace">
                    <select name="preferedMonth", id="preferedMonth">
                        <option value="">Choose Prefered Month</option>
                            {!! monthListings() !!}
                    </select>
                </div>
        </div>

        <div class="ml-3 hidden" id="weekPlaceHolder">
                <div id="weekPlace">
                    <input type="date" id="loadingSiteWeekOne" >
                    <input type="date" id="loadingSiteWeekTwo" >
                    <button id="searchLoadingSiteByWeek" style="font-size:10px;" class="font-weight-semibold">VIEW</button>
                </div>
        </div>

        <div class="ml-3 hidden" id="dayPlaceHolder">
                <div id="dayPlace"><input type="date" id="specificDay" name="specific_day"></div>
        </div>

    </section>

    <div class="row mt-1 ml-2 mr-2">
        <div class="col-md-12" id="masterbarcharholder">
            <canvas id="masterBarChart" height="100"></canvas>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-header header-elements-sm-inline">
            <h6 class="card-title">Support tickets</h6>
        </div>

        <div class="card-body d-md-flex align-items-md-center justify-content-md-between flex-md-wrap">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <div id="tickets-status"><svg width="42" height="42"><g transform="translate(21,21)"><g class="d3-arc" style="stroke: rgb(255, 255, 255); cursor: pointer;"><path style="fill: rgb(41, 182, 246);" d="M1.1634144591899855e-15,19A19,19 0 0,1 -12.326087772183463,-14.459168725498339L-6.163043886091732,-7.229584362749169A9.5,9.5 0 0,0 5.817072295949927e-16,9.5Z"></path></g><g class="d3-arc" style="stroke: rgb(255, 255, 255); cursor: pointer;"><path style="fill: rgb(102, 187, 106);" d="M-12.326087772183463,-14.459168725498339A19,19 0 0,1 14.331188229058796,-12.474656065130077L7.165594114529398,-6.237328032565038A9.5,9.5 0 0,0 -6.163043886091732,-7.229584362749169Z"></path></g><g class="d3-arc" style="stroke: rgb(255, 255, 255); cursor: pointer;"><path style="fill: rgb(239, 83, 80);" d="M14.331188229058796,-12.474656065130077A19,19 0 0,1 5.817072295949928e-15,19L2.908536147974964e-15,9.5A9.5,9.5 0 0,0 7.165594114529398,-6.237328032565038Z"></path></g></g></svg></div>
                <div class="ml-3">
                    <h5 class="font-weight-semibold mb-0">3239 <span class="text-success font-size-sm font-weight-normal"><i class="icon-arrow-up12"></i> (+2.9%)</span></h5>
                    <span class="badge badge-mark border-success mr-1"></span> <span class="text-muted">Sept 20, 02:30 PM</span>
                </div>
            </div>

            <div class="d-flex align-items-center mb-3 mb-md-0">
                <a href="#" class="btn bg-transparent border-indigo-400 text-indigo-400 rounded-round border-2 btn-icon">
                    <i class="icon-alarm-add"></i>
                </a>
                <div class="ml-3">
                    <h5 class="font-weight-semibold mb-0">1,132</h5>
                    <span class="text-muted">total tickets</span>
                </div>
            </div>

            <div class="d-flex align-items-center mb-3 mb-md-0">
                <a href="#" class="btn bg-transparent border-indigo-400 text-indigo-400 rounded-round border-2 btn-icon">
                    <i class="icon-spinner11"></i>
                </a>
                <div class="ml-3">
                    <h5 class="font-weight-semibold mb-0">06:25:00</h5>
                    <span class="text-muted">Average response time</span>
                </div>
            </div>

            <div>
                <a href="#" class="btn bg-teal-400"><i class="icon-statistics mr-2"></i> Report</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 50px">Due</th>
                        <th style="width: 300px;">User</th>
                        <th>Description</th>
                        <th class="text-center" style="width: 20px;"><i class="icon-arrow-down12"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-active table-border-double">
                        <td colspan="3">Active tickets</td>
                        <td class="text-right">
                            <span class="badge bg-blue badge-pill">24</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-center">
                            <h6 class="mb-0">12</h6>
                            <div class="font-size-sm text-muted line-height-1">hours</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <a href="#" class="btn bg-teal-400 rounded-round btn-icon btn-sm">
                                        <span class="letter-icon">O</span>
                                    </a>
                                </div>
                                <div>
                                    <a href="#" class="text-default font-weight-semibold letter-icon-title">Flour Mills</a>
                                    <div class="text-muted font-size-sm"><span class="badge badge-mark border-blue mr-1"></span> Active</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="#" class="text-default">
                                <div class="font-weight-semibold">[#1183] Issues on SOKOTO Order</div>
                                <span class="text-muted">What's the status report on this Sokoto Order...</span>
                            </a>
                        </td>
                        <td class="text-center">
                            <div class="list-icons">
                                <div class="list-icons-item dropdown">
                                    <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown"><i class="icon-menu7"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item"><i class="icon-undo"></i> Quick reply</a>
                                        <a href="#" class="dropdown-item"><i class="icon-history"></i> Full history</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="#" class="dropdown-item"><i class="icon-checkmark3 text-success"></i> Resolve issue</a>
                                        <a href="#" class="dropdown-item"><i class="icon-cross2 text-danger"></i> Close issue</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>

    <input type="hidden" value="{{date('F, Y')}}" id="currentMonthInTheYear">

</div>
<!-- /main content -->
<?php
    $current_day = date('d');
    $monthInView = date('M');
    for($i=1; $i<= $current_day; $i++){
        $daysIntheMonth[] = $i.date("S-", mktime(0, 0, 0, 0, $i, 0)).$monthInView;
    }
?>

@stop


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script type="text/javascript" src="{{URL::asset('/js/validator/excelme.js')}}"></script>
<script>
        $('.container').hide();
        $('button').click(function(){
            var target = "#" + $(this).data("target");
            $(".container").not(target).hide();
            $(target).show();
        });
    var defaultbgcolors = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
    ]
    var waybillbgcolors = [
        'rgb(0,128,0, 1)',
        'rgb(255,255,0, 1)',
        'rgb(255,0,0, 1)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'      
    ]

    var targetProcessChart = document.getElementById('targetProcessChart');
    var targetForTheMonth = <?php echo json_encode($targetforthemonth); ?>;
    var gateOutForTheMonth = <?php echo json_encode($gatedOutForTheMonth); ?>;
    var remainder;
    var remainderTarget = targetForTheMonth - gateOutForTheMonth;
    if(remainderTarget <= 0) {
        remainder = -1 *  remainderTarget  
    }
    else {
        remainder = remainderTarget
    }
    var gateOutStatistics = [remainder, gateOutForTheMonth];
    var currentMonth = $('#currentMonthInTheYear').val();
    var targetChart = new Chart(targetProcessChart, {
        type: 'doughnut',
        data: {
            labels: [currentMonth, 'Completed'],
            datasets: [{
                label: 'Monthly Target Statistics',
                data: gateOutStatistics,
                backgroundColor: [
                    'rgba(255, 0, 0, 0.7)', //uncomment this 
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    //'rgb(124,252,0)', // comment this
                    'rgba(54, 162, 235, 0.2)',
                    
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
    });

    $('#targetProcessChart').click(function() {
        $currentYear = $('#currentYear').val();
        $month = $('#previousMonthTarget').val();
        $('#currentMonthGateOutTripListings').html('<i class="icon-spinner3 spinner mr-1"></i>loading...').addClass("ml-2 font-weight-semibold")
        $.get('/current-month-trip-details', {year: $currentYear, month: $month }, function(data) {
            $('#currentMonthGateOutTripListings').html(data)
        })
    })

    $('#previousMonthTarget').change(function() {
        $year = $('#currentYear').val();
        $month = $(this).val();
        $.get('/monthly-target-graph', { year: $year, month: $month }, function(data) {
            $gateOutForTheMonth = data[0];
            $targetForTheSelectedMonth = data[1].target;
            $remainder = $targetForTheSelectedMonth - $gateOutForTheMonth;
            targetChart.data.labels[0] = 'Target' 
            targetChart.data.datasets[0].data = [$remainder, $gateOutForTheMonth]
            $('#gateOutMonthPlaceholder').text($gateOutForTheMonth);
            $('#targetPlaceholder').text($targetForTheSelectedMonth);
            $percentage = Math.round(($gateOutForTheMonth / $targetForTheSelectedMonth) * 100);
            $('.percentageHolder').html($percentage+'% of '+$targetForTheSelectedMonth);
            targetChart.update()
        })
    })

    $stagesOfOperation = <?php echo json_encode($stagesOfOperation); ?>;
    var operations = chartPlotter('bar', 'masterTripChart', 
        ['Gate In', 'At Loading Bay', 'Departed Loading Bay', 'On Journey', 'At Destination', 'Offloaded'], 
        'Trip Status', $stagesOfOperation, defaultbgcolors
    )

    function chartPlotter(type, placeholder, labels, label, data, backgroundColorCode) {
        var placeholderName = document.getElementById(placeholder);
        var placeholderChart = new Chart(placeholderName, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: backgroundColorCode,
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
            },
        });
    }

// <!-- Chart for waybill Process -->
    var waybillStatusCategory = <?php echo json_encode($waybillCategories); ?>;
    chartPlotter('bar', 'waybillChart', ['Good', 'Warning', 'Danger'], 'Waybill Status', waybillStatusCategory, waybillbgcolors);


// <!-- Chart for Daily Gate out -->
    var dayssofar = <?php echo json_encode($daysIntheMonth); ?>;
    var noOfTripsPerDay = <?php echo json_encode($noOfTripsPerDay); ?>;
    var currentMonth = $('#currentMonthInTheYear').val();
    chartPlotter('line', 'dailyGateOutChart', dayssofar, currentMonth, noOfTripsPerDay, defaultbgcolors)

    var gatedOutDailyArray = <?php echo json_encode($numberofdailygatedout); ?>;
    var today = new Date();
    var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
    chartPlotter('bar', 'myProjectionChart', ['Gated Out, Today'], 'Gated Out for (Today): '+date, [gatedOutDailyArray], defaultbgcolors);

    var monthComparator = document.getElementById('gatedOutForTheMonth');
    var gatedOutForTheMonth = <?php echo json_encode($gatedOutForTheMonth); ?>;
    var currentMonth = $('#currentMonthInTheYear').val();
    var monthComparatorChart = new Chart(monthComparator, {
        type: 'bar',
        data: {
            labels: [currentMonth],
            datasets: [{
                label: ['Current Month in View'],
                data: [gatedOutForTheMonth],
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

    $('#compareTheTwoMonths').click(function(){
        $firstMonthComparator = $('#firstMonthComparator').val();
        $secondMonthComparator = $('#secondMonthComparator').val();
        $.get('/gatedout-months-comparison', {firstMonth:$firstMonthComparator, secondMonth:$secondMonthComparator}, function(response){
            monthComparatorChart.data.labels = [monthName($firstMonthComparator), monthName($secondMonthComparator)]
            monthComparatorChart.data.datasets[0].data = response
            monthComparatorChart.update()
        })
    })

    function monthName(month) {
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        return months[month-1]; 
    }


    /** default last One week and any date ranger */
    var dateRange = document.getElementById('gatedOutForTheWeek');
    var gatedoutTripFortheWeek = $('#gatedOutForTheWeekValue').val();
    var currentWeekInView = $('#currentWeekInView').val();
    var presentDay = $('#presentDay').val();
    var dateRangeChart = new Chart(dateRange, {
        type: 'bar',
        data: {
            labels: ['Gated Out Trips for Week in view'],
            datasets: [{
                label: `${currentWeekInView} - ${presentDay}`,
                data: [gatedoutTripFortheWeek],
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

    $('.chartOfDateRange').click(function() {
        $dateFrom = $('#currentWeekInView').val();
        $presentDay = $('#presentDay').val();
        $('#specificDataRangeRecord').html('<i class="icon-spinner3 spinner"></i> Loading...').addClass('ml-2 font-weight-bold');
        $.get('/gatedout-selected-week', {dateFrom: $dateFrom, currentDay: $presentDay}, function(data) {
            $noOfTrips = data[0][0].weeklygateout;
            $record = data[1]
            dateRangeChart.data.datasets[0].data = [$noOfTrips]
            $('#specificDataRangeRecord').html($record)
            dateRangeChart.update();
        });
    })



    /** Loading site count */
    var loadingSiteCount = document.getElementById('masterBarChart');
    var dailyLoadingSiteCount = <?php echo json_encode($countDailyTripByLoadingSite); ?>;
    var loadingSites = <?php echo json_encode($loading_sites); ?>;
    var loadingSiteCountChart = new Chart(loadingSiteCount, {
        type: 'bar',
        data: {
            labels: loadingSites,
            datasets: [{
                label: 'Daily Charts of Loading Sites',
                data: dailyLoadingSiteCount,
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

    $('#specificDay').blur(function(){
        $choosenDay = $('#specificDay').val();
        $.get('/loading-site-specific-day', {choosen_day:$choosenDay}, function(response){
            loadingSiteCountChart.data.labels = response[1]
            loadingSiteCountChart.data.datasets[0].label = 'loading site operation count on '+$choosenDay
            loadingSiteCountChart.data.datasets[0].data = response[0]
            loadingSiteCountChart.update(); 
        });
    });

    $('#preferedMonth').change(function(){
        $selected_month = $('#preferedMonth').val();
        $.get('/loading-site-monthly', {selected_month:$selected_month}, function(response){
            loadingSiteCountChart.data.labels = response[1]
            loadingSiteCountChart.data.datasets[0].label = 'Monthly count by loading site'
            loadingSiteCountChart.data.datasets[0].data = response[0]
            loadingSiteCountChart.update();
        });
    });

    $('#searchLoadingSiteByWeek').click(function(){
        $loadingSiteWeekOne = $('#loadingSiteWeekOne').val();
        $loadingSiteWeekTwo = $('#loadingSiteWeekTwo').val();
        $.get('/loading-site-weekly', {weekOne:$loadingSiteWeekOne, weekTwo:$loadingSiteWeekTwo}, function(response){
            loadingSiteCountChart.data.labels = response[1]
            loadingSiteCountChart.data.datasets[0].label = 'Operation between '+$loadingSiteWeekOne+' and '+$loadingSiteWeekTwo
            loadingSiteCountChart.data.datasets[0].data = response[0]
            loadingSiteCountChart.update();
        });
    });

    
</script>


@stop

@endif