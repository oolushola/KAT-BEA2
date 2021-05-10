@extends('layout')

@section('title') Performance Metric - {{ucfirst(Auth::user()->first_name)}} {{ucfirst(Auth::user()->last_name)}} @stop

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
<link rel="stylesheet" type="text/css" href="{{URL::asset('/css/custom.css')}}">
@stop

@section('main')

<?php 
    date_default_timezone_set('Africa/Lagos');
    $numeric_date = date('G');
    if($numeric_date >=0 && $numeric_date <=11){
        $timeOfTheDay = 'Morning';
    }
    elseif($numeric_date >= 12 && $numeric_date <= 17){
        $timeOfTheDay = 'Afternoon';
    }
    else{
        $timeOfTheDay = 'Evening';
    }
?>

<div class="content-wrapper">
    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4 class="text-primary"><i class="icon-sun3 text-warning"></i> <span class="font-weight-semibold"> 
                    Good {{ $timeOfTheDay }}, 
                    {{ Auth::user()->first_name }}
                    {{ substr(Auth::user()->last_name, 0,1)}}.</span>
                </h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <a href="#overAllGateTrips" data-toggle="modal" class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-danger font-weight-bold">{{ count($totalTripsData) }}</h2>
                        <p class="text-primary font-weight-bold">TOTAL GATE OUT</p>
                    </a>
                    <a href="#myCurrentMonthGateOut" data-toggle="modal"  class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-primary font-weight-bold">{{ count($currentMonthData) }}</h2>
                        <p class="text-primary font-weight-bold">{{ strtoupper(date('F')) }} TRIPS</p>
                    </a>
                    <a href="#truckAvailability" data-toggle="modal" href=""  class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-info font-weight-bold">{{ count($availableTrucks) }}</h2>
                        <p class="text-primary font-weight-bold">TRUCK AVAILABILITY</p>
                    </a>
                    <a href="#yetToGateOut" data-toggle="modal"  class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-warning font-weight-bold">{{ count($yetTogateOut) }}</h2>
                        <p class="text-primary font-weight-bold">YET TO GATE OUT</p>
                    </a>
                    
                    
                </div>
            </div>
            
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i>Performance Metric</a>
                    <span class="breadcrumb-item active"> For the Month of : {{ date('F') }}</span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

           
        </div>
    </div>
    <!-- /page header -->

    <div class="row">
        <div class="col-md-4 col-sm-12 mb-2">
            <div class="dashboardbox">
                <table class="table">
                    <tr>
                        <td><i class="icon-trophy3 text-warning-400 icon"></i></td>
                        <td class="font-weight-semibold text-center" id="target-label">Current Month Target</td>
                        <td>
                            <select class="dashboard-select" id="previousMonthTarget" name="previous_month_target">
                                <option>Previous Months</option>
                            </select></td>
                    </tr>
                </table>
                <hr class="mt-5-negative">
                <canvas id="myTarget" height="200" data-toggle="modal" href="#totalGateOutForTheMonth"></canvas>
               
                <p class="font-weight-bold" style="display:inline-block">GTV: ₦{{ number_format($gtvForCurrentMonth, 2) }}</p>
                <p class="font-weight-bold" style="display:inline-block; float:right">TR: ₦{{ number_format($trForCurrentMonth, 2) }}</p>
                <!-- <p id="target-value"><span class="text-primary-400">30</span> of <span class="text-danger-400">40</span></p>  -->

                <p class="text-center text-danger font-weight-bold">
                    <?php $status = $currentMonthRateDiff / $buhCurrentMonthTarget * 100; ?>
                    <span class="text-primary">%{{ number_format($status,2) }}</span> of ₦{{ number_format($buhCurrentMonthTarget, 2) }}
                </p>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 mb-2">
            <div class="dashboardbox">
                <canvas id="masterTripChart" height="300">
                </canvas>
            </div>
            
        </div>

        <div class="col-md-2 col-sm-12 col-xs-12 mb-2">
            <div class="dashboardbox">
                <canvas id="marginAndMarkup" height="700"></canvas>
            </div>    
        </div>

        <div class="col-md-2 col-sm-12 mb-2">
            <?php
                if($status < 0){
                    $ratings = 0;
                    $stars = '';
                    $remark = 'Worrisome';
                }
                elseif($status >= 0 && $status <= 9) {
                    $ratings = 0;
                    $stars = '';
                    $remark = 'Too Bad';
                }
                elseif($status >= 10 && $status <= 19.9){
                    $ratings = 1;
                    $stars = '<i class="icon-star-full2"></i>';
                    $remark = 'Too Bad';
                }
                elseif($status >= 20 && $status <= 39.9){
                    $ratings = 2;
                    $stars = '<i class="icon-star-full2"></i><i class="icon-star-full2"></i>';
                    $remark = 'Fair';
                }
                elseif($status >= 40 && $status <= 59.9){
                    $ratings = 3;
                    $stars = '<i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i>';
                    $remark = 'Good';
                }
                elseif($status >= 60 && $status <= 79.9){
                    $ratings = 4;
                    $stars = '<i class="icon-star-full2"></i> <i class="icon-star-full2"></i> <i class="icon-star-full2"></i> <i class="icon-star-full2"></i>';
                    $remark = 'Impressive';
                }
                elseif($status >=80 && $status <=100){
                    $ratings = 5;
                    $stars = '<i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i><i class="icon-star-full2"></i>';
                    $remark = 'Goal Getter';
                }
                else{
                    $ratings = '<i class="icon-trophy3"></i>';
                    $stars = '<i class="icon-medal"></i> <i class="icon-medal"></i> <i class="icon-medal"></i> <i class="icon-medal"></i> <i class="icon-medal"></i>';
                    $remark = 'Golden Buzzer';
                }
                ?>
            
            <div class="dashboardbox">
                <p class="text-center font-weight-bold text-primary">{!! $stars !!}</p>

                <h1 class="mt-4 text-center text-primary font-weight-bold">OVERALL RATING</h1>

                <h2 class="mt-4 text-center font-weight-bold text-warning">{!! $ratings !!} of 5</h2>

                <h5 class="mt-4 text-center font-weight-bold text-warning">REMARK</h5>
                <h1 class="font-weight-bold text-center text-danger ">{{ $remark }}</h1>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="row">
            <div class="col-md-7 col-sm-12">
                <div class="">
                    <canvas id="dailyGateOutChart"></canvas>
                </div>
            </div>
            <div class="col-md-5 col-sm-12 mb-2 mt-3">
                <div class="table-responsive" style="max-height:310px">
                    <table class="table table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th colspan="4" style="font-size:11px; font-weight:bold" class="text-danger">PENDING PAYMENT APPROVAL</th>
                            </tr>
                            <tr style="font-size:11px;">
                                <th class="font-weight-bold text-primary">Trip ID</th>
                                <th class="font-weight-bold text-primary">Destination</th>
                                <th class="font-weight-bold text-primary">Payment for?</th>
                                <th class="font-weight-bold text-primary">Amount</th>
                            </tr>
                            <tbody style="font-size:11px; font-weight:bold">
                            @if(count($pendingPayments))
                            <?php $sumTotalOfAmount = 0; ?>
                            @foreach($pendingPayments as $pendingPayment)
                                <tr>
                                    <td>{{ $pendingPayment->kaid }}</td>
                                    <td>{{ $pendingPayment->exact_location_id }}</td>
                                    <?php 
                                        if($pendingPayment->advance_paid == FALSE){
                                            $mode = 'Advance';
                                            $amount = $pendingPayment->advance;
                                        }
                                        elseif ($pendingPayment->advance_paid == TRUE && $pendingPayment->balance_paid == FALSE) {
                                            $mode = 'Balance';
                                            $amount = $pendingPayment->balance;
                                        }
                                        else{
                                            if($pendingPayment->advance_paid == TRUE && $pendingPayment->balance_paid == TRUE && $pendingPayment->outstanding_balance != ''){
                                                $mode = 'Outstanding';
                                                $amount = $pendingPayment->outstanding_balance;
                                            }
                                        }
                                        $sumTotalOfAmount += $amount;
                                    ?>
                                    <td>{{$mode}}</td>
                                    <td>₦{{number_format($amount,2)}}</td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td class="table-danger text-primary" colspan="5">Total Payment: ₦{{ number_format($sumTotalOfAmount,2) }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="4" class="table-info font-size-sm">Kaya is currently not owing any of your transporters.</td>
                                </tr>
                            @endif
                            </tbody>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
        $current_day = date('d');
        $monthInView = date('M');
        for($i=1; $i<= $current_day; $i++){
            $daysIntheMonth[] = $i.date("S-", mktime(0, 0, 0, 0, $i, 0)).$monthInView;
        }
    ?>

</div>

@include('performance-metric.partials._truckAvailability')
@include('performance-metric.partials._yettogateout')
@include('performance-metric.partials._myoverallgateout')
@include('performance-metric.partials._mycurrentMonthTrips')

@stop


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>


<script>

    $('#clientRateholder').html('₦'+$('#totalClientRate').val());
    $('#transporterRateHolder').html('₦'+$('#totalTransporterRate').val());
    $('#grossMarginHolder').html('₦'+$('#totalGrossMargin').val());
    $('#averageMarginHolder').html('%'+$('#averageMargin').val());

    var targetForTheMonth = <?php echo json_decode($buhCurrentMonthTarget) ?>;
    var gateOutForTheMonth = <?php echo json_decode($currentMonthRateDiff) ?>;
    if(gateOutForTheMonth < 0){
        gateOutForTheMonth = 0;
    } 

    var remainderTarget = targetForTheMonth - gateOutForTheMonth;
    var gateOutStatistics = [remainderTarget, gateOutForTheMonth];

    doughnutPie('myTarget', ['Expected Margin', 'Achieved'], 'Target', gateOutStatistics)

    // for over all gtv
    var overallGtv = <?php echo json_encode($overallGtv); ?>;
    var overAllTr = <?php echo json_encode($overAllTr); ?>;
    var overallDiff = <?php echo json_decode($overallDiff); ?>;
    
    myBarChart('bar', 'masterTripChart', ['GTV', 'TR', 'Gross Margin'], 'Income (₦) ', [overallGtv, overAllTr, overallDiff])

    var percentageMargin = overallDiff / overallGtv  * 100;

    myBarChart('bar', 'marginAndMarkup', ['Gross Margin (%)'], 'Margin', [percentageMargin.toFixed(2)])

    lineChart()


    function myBarChart(chartType, placeholder, labels, chartLabel, actualData) {
        var ctx = document.getElementById(placeholder);
        var myChart = new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: chartLabel,
                    data: actualData,
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
                }
                
            
            ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                if(parseInt(value) >= 1000){
                                return '₦' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                } else {
                                return '' + value;
                                }
                            }
                        }
                    }]
                },
            },
        });
    }

    function doughnutPie(placeholder, labels, label, data) {
        var ctx = document.getElementById(placeholder);
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: [
                        'rgba(255, 0, 0, 0.7)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderWidth: 1
                }]
            },

        });
    }

    function lineChart(){ 
        var ctx = document.getElementById('dailyGateOutChart');
        var dayssofar = <?php echo json_encode($daysIntheMonth); ?>;
        var noOfTripsPerDay = <?php echo json_encode($buhNoOfGatePerDayInCurrentMonth); ?>;
        

        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dayssofar,
                datasets: [{
                    label: 'Current Month Daily Gate Out Trajectory',
                    data: noOfTripsPerDay,
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
    }


    $('#unlockClientRate').click(function(){
        $(this).addClass("hidden");
        $('#lockClientRate').removeClass('hidden');
        $('#submitClientRate').removeClass('hidden');
        $('.defaultClientRate').addClass('hidden');
        $('.clientRate').removeClass('hidden');
        $('#clientRateTitle').addClass('hidden');
    });
   
    $('#lockClientRate').click(function(){
        $(this).addClass("hidden");
        $('#unlockClientRate').removeClass('hidden');
        $('#submitClientRate').addClass('hidden');
        $('.defaultClientRate').removeClass('hidden');
        $('.clientRate').addClass('hidden');
        $('#clientRateTitle').removeClass('hidden');
    });

    $('#submitClientRate').click(function(e) {
        e.preventDefault();
        $(this).html('<i class="spinner icon-spinner2"></i> Please wait...').attr('disabled', 'disabled');
        $event = $(this);
        $.post('/update-client-rate', $('#frmUpdateClientRate').serializeArray(), function(data) {
            if(data == "saved") {
                $event.html('<i class="icon-checkmark2"></i> Saved Successfully');
                setTimeout(() => {
                    window.location.href='';
                }, 2000);
            }
            else{
                alert('Oops! Something went wrong. Try again later.');
                return false;
            }
        })
    })


    $('#unlockTransporterRate').click(function(){
        $(this).addClass("hidden");
        $('#lockTransporterRate').removeClass('hidden');
        $('#submitTransporterRate').removeClass('hidden');
        $('.defaultTransporterRate').addClass('hidden');
        $('.transporterRate').removeClass('hidden');
        $('#transporterRateTitle').addClass('hidden');
    });
   
    $('#lockTransporterRate').click(function(){
        $(this).addClass("hidden");
        $('#unlockTransporterRate').removeClass('hidden');
        $('#submitTransporterRate').addClass('hidden');
        $('.defaultTransporterRate').removeClass('hidden');
        $('.transporterRate').addClass('hidden');
        $('#transporterRateTitle').removeClass('hidden');
    });

    $('#submitTransporterRate').click(function(e) {
        e.preventDefault();
        $(this).html('<i class="spinner icon-spinner2"></i> Please wait...').attr('disabled', 'disabled');
        $event = $(this);
        $.post('/update-transporter-rate', $('#frmUpdateClientRate').serializeArray(), function(data) {
            if(data == "saved") {
                $event.html('<i class="icon-checkmark2"></i> Saved Successfully');
                setTimeout(() => {
                    window.location.href='';
                }, 2000);
            }
            else{
                alert('Oops! Something went wrong. Try again later.');
                return false;
            }
        })
    })

    $('#showTrucks').click(function() {
        $('.defaultTruckDisplay').addClass('d-none')
        $('.hideTruckUpdate').removeClass('d-none')
    })

    $('#hideTruck').click(function() {
        $('.defaultTruckDisplay').removeClass('d-none')
        $('.hideTruckUpdate').addClass('d-none')  
    })

    $('#updateTruckNo').click(function($e) {
        $e.preventDefault();
        $(this).attr('disabled', 'disabled')
        $(this).html('<i class="icon-spinner3 spinner"></i> Please wait...').addClass('font-size-xs')
        $targetEvent = $(this)
        $.post('/performancemetric-truckno-update', $('#frmUpdateClientRate').serializeArray(), function(data) {
            if(data == 'updated') {
                $targetEvent.html('<i class="icon-checkmark2"></i> Completed')
                window.location = ''
            }
            else {
                alert('Oops! something went wrong')
                return false
            }
        })
    })

</script>






@stop

