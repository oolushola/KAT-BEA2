@extends('layout')

@section('title') Performance Metric - {{ucfirst(Auth::user()->first_name)}} {{ucfirst(Auth::user()->last_name)}} @stop

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
<link rel="stylesheet" type="text/css" href="{{URL::asset('/css/custom.css?v=time()')}}">
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
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Performance Metric</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="d-flex justify-content-center">
                    <h4 class="text-primary"><i class="icon-sun3 text-warning"></i> <span class="font-weight-semibold"> 
                    Good {{ $timeOfTheDay }}, 
                    {{ ucfirst(substr(Auth::user()->first_name, 0,1)) }}
                    {{ ucfirst(substr(Auth::user()->last_name, 0,1))}}.</span>
                    </h4>
                </div>
            </div>
            
        </div>

        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="{{URL('performance-metrics')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i>Performance Metric</a>
                    <span class="breadcrumb-item active"> For 
                        <select class="dashboard-select" style="margin-top:0px; outline:none" id="currentYear">
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
                        <select class="dashboard-select" style="margin-top:0px; outline:none" id="currentMonth">
                            <?php 
                                $currentMonth = date('m');
                                for($i=1; $i<=12; $i++){
                                    $month = date('F', mktime(0,0,0,$i, 1, date('Y')));
                                    if($currentMonth == $i) {
                                        echo '<option value="'.$month.'" selected>'.$month.'</option>';
                                    }
                                    else {
                                        echo '<option value="'.$month.'">'.$month.'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </span>
                    <span id="loader"></span>
                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
    </div>
    <!-- /page header -->

    <div class="row">
        <div class="col-md-7">
            <canvas id="stackedBar" height="150"></canvas>
        </div>
        <div class="col-md-5 col-sm-12 mb-2">
            <canvas id="buhCurrentMonthMargin" height="215"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="row">
            <section class="col-md-12 m-2">
                <span class="ml-2 font-size-xs">From: <input type="date" id="datedFrom" style="border:1px solid #000; width: 120px; font-size: 10px; outline: none"> </span>
                <span class="ml-2 font-size-xs">To: <input type="date" id="datedTo" style="border:1px solid #000; width: 120px; font-size: 10px; outline: none"></span>
                <button id="shootStatAnalysis" class="font-weight-bold" style="border:1px solid #000; font-size: 10px; outline: none"> SHOOT</button>
            </section>
            <div class="col-md-6">
                <canvas id="numbersForTheMonth" height="150" data-toggle="modal" href="#specificBuh"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="newTransporterGained" height="150" data-toggle="modal" href="#transporterGained"></canvas>
            </div>
        </div>
        <div class="" id="performanceAnalysis"></div>
        <span class="d-none ml-4 mb-4 font-weight-semibold pointer text-danger" id="closePerformanceAnalysis">Close</span>
    </div>

    <!-- <div class="card"> -->
    <div class="row">
        <div class="col-md-6">
            <canvas id="bonusAndEarnings" height="200"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="expectedTripsFromClient" height="225"></canvas>
        </div>
    </div>
    <!-- </div> -->
</div>

@include('performance-metric.partials._specificNumberPerformance')
@include('performance-metric.partials._transporter_gained')

@stop


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script>

    var unitHeadInformations = <?php echo json_encode($unitHeadInformation); ?>;
    var expectedMargin = <?php echo json_encode($unitHeadSpecificTargets); ?>;
    var achieved = <?php echo json_encode($myGrossMargin); ?>;
    var deficit = <?php echo json_encode($myOutstanding); ?>;
    var currentMonthMarkup = <?php echo json_encode($unitHeadCurrentMarkUp); ?>;
    var tripCount = <?php echo json_encode($trip_count); ?>;
    var remainingTrips = <?php echo json_encode($remainingTrip); ?>;
    var transporterGained = <?php echo json_encode($transporter_gained); ?>;

    var ctx = document.getElementById('stackedBar');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: unitHeadInformations,
            datasets: [
                {
                    label: 'Expected Margin',
                    data: expectedMargin,
                    backgroundColor: 'green',
                    borderWidth: 1
                },
                {
                    label: 'Achieved',
                    data: achieved,
                    backgroundColor: '#7EF9FF',
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
                            return '₦' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+'M';
                            } else {
                            return '₦' + value+'M';
                            }
                        },   

                    }
                }]
            },
        },
    });

    const backgroundColor = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
    ]
    const borderColor = [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
    ]

    var ctxMarkup = document.getElementById('buhCurrentMonthMargin');
    var markUpChart = new Chart(ctxMarkup, {
        type: 'bar',
        data: {
            labels: unitHeadInformations,
            datasets: [{
                label: 'Current Markup',
                data: currentMonthMarkup,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
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

    var lineChartCtx = document.getElementById('dailyGateOutChart');
    var specificDayDateLineChart = new Chart(lineChartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'My Daily Gate out for the current month',
                data: [],
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
    })
    
    var targetCtx = document.getElementById('targetDoughnut');
    var targetChart = new Chart(targetCtx, {
        type: 'doughnut',
        data: {
            labels: ['Expected Margin', 'Achieved'],
            datasets: [{
                label: 'Target',
                data: [100, 0],
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

    var selectedMonthMarkupCtx = document.getElementById('selectedMonthMarkup');
    var selectedMonthMarkUpChart = new Chart(selectedMonthMarkupCtx, {
        type: 'bar',
        data: {
            labels: ['Gross Margin (%)'],
            datasets: [{
                label: 'Margin',
                data: [0],
                backgroundColor: backgroundColor,
                borderColor: borderColor,
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
    
    var ctxNumberOfTrips = document.getElementById('numbersForTheMonth');
    var numberOfTripsChart = new Chart(ctxNumberOfTrips, {
        type: 'bar',
        data: {
            labels: unitHeadInformations,
            datasets: [
                {
                    label: 'Number of Trips Done',
                    data: tripCount,
                    backgroundColor: '#73c2fb',
                    borderWidth: 1
                },
                {
                    label: 'Expected Number of Trips',
                    data: remainingTrips,
                    backgroundColor: '#ff6347',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    stacked: true
                }],
                xAxes: [{
                    stacked: true
                }]
            },
            
            onClick: function(c, i) {
                e = i[0];
                var xValue = this.data.labels[e._index];
                var yValue = this.data.datasets[0].data[e._index];
                $('#loaderSpecific').html('<i class="icon-spinner3 spinner"></i>Loading...')
                $.get('/specific-buh-performance', { user: xValue, year: $('#currentYear').val(), month: $('#currentMonth').val() }, function(res) {
                    $period = $('#currentMonth').val()+', '+$('#currentYear').val()
                    $('#loaderSpecific').html($period+' Performance Review of, '+res.fullName+'.')
                    $('#selectedMonthAndData').html(res.selectedMonthData)
                    specificDayDateLineChart.data.labels = res.daysIntheMonth
                    specificDayDateLineChart.data.datasets[0].label = $period
                    specificDayDateLineChart.data.datasets[0].data = res.dailyGateOutForCurrentMonth 
                    specificDayDateLineChart.update()

                    var remainder = res.selectedMonthTarget.target - res.profitGenerated
                    targetChart.data.datasets[0].data = [remainder, res.profitGenerated]
                    targetChart.update()
                    
                    $('#gtvText').html(res.revenueGenerated)
                    $('#trText').html(res.transporterRate)
                    $('#targetPercentage').html(res.percentageProfit)
                    $('#targetAmount').html(res.target)
                    $('#averageRatingsChart').html(res.ratingsChart)
                    console.log(res.percentageMarkUp)
                    selectedMonthMarkUpChart.data.datasets[0].data = [res.percentageMarkUp]
                    selectedMonthMarkUpChart.update()

                    $('#yetToGateOutRecord').html(res.tripsYetToGateOut)

                    $('#modalBody').removeClass('d-none')
                })
            }
        },
    });

    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    var date = new Date();
    var ctxTransporters = document.getElementById('newTransporterGained');
    var transporterChart = new Chart(ctxTransporters, {
        type: 'bar',
        data: {
            labels: unitHeadInformations,
            datasets: [{
                label: 'Transporter Gained for '+months[date.getMonth()]+','+date.getFullYear(),
                data: transporterGained,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
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
            onClick:function(c, i) {
                e = i[0];
                var xValue = this.data.labels[e._index];
                var yValue = this.data.datasets[0].data[e._index];
                $('#transporterGainedBuh').html('<i class="icon-spinner3 spinner"></i>Please wait...')
                $.get('/buh-transporter-gained', { user: xValue, year: $('#currentYear').val(), month: $('#currentMonth').val() }, function(res) {
                    $period = $('#currentMonth').val()+', '+$('#currentYear').val()
                    $('#transporterGainedBuh').html(xValue+' onboarded '+yValue+' transporters for, '+$period)
                    
                    $('#transporterGainedList').html(res)
                    $('#modalBody').removeClass('d-none')
                })
            }
        },
    });

    var clientNames = <?php echo json_encode($clientNames); ?>;
    var tripDoneWithClient = <?php echo json_encode($tripDoneWithClient); ?>;
    var pendingTrips = <?php echo json_encode($pendingTrips); ?>;

    var ctxClientExpectedTrips = document.getElementById('expectedTripsFromClient');
    var clientExpectedTripsChart = new Chart(ctxClientExpectedTrips, {
        type: 'bar',
        data: {
            labels: clientNames,
            datasets: [
                {
                    label: 'Number of Trips Done',
                    data: tripDoneWithClient,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderWidth: 1
                },
                {
                    label: 'Expected Number of Trips',
                    data: pendingTrips,
                    backgroundColor: 'rgba(255, 0, 0, 0.7)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    stacked: true
                }],
                xAxes: [{
                    stacked: true
                }]
            },
            title: {
                    display: true,
                    position: 'top',
                    text: 'Client trip count for,  '+months[date.getMonth()]+' '+date.getFullYear()
                }
        },
    });

    $(document).on('keyup', '#searchPreviousTripsOfSelectedDate', function() {
        $value = $(this).val().toLowerCase()
        $(`#selectedDateDataRecord tr`).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf($value) > -1)
        });
    })

    var totalBonus = <?php echo json_encode($totalBonus); ?>;
    var ctx = document.getElementById('bonusAndEarnings');
    var bonusBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: unitHeadInformations,
            datasets: [
                {
                    label: 'Bonus',
                    data: totalBonus,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
                display: true
            },
            
            title: {
                display: true,
                text: 'Extra Earnings (₦) - For The Year In View.',
                position: 'top'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
        },
    });

    $('#currentMonth').change(function() {
        $year = $('#currentYear').val();
        $month = $('#currentMonth').val();
        $('#loader').html('<i class="icon-spinner3 spinner"></i>Please wait...').addClass('mt-2 ml-2')
        $.get('/filter-performance-metrics', { current_year: $year, current_month: $month }, function(res) {
            if(res === 'NoTarget') {
                $('#loader').html('<i class=""></i>Oops! We do no not have business unit head information for the selected date ').addClass('font-size-xs font-weight-semibold').fadeIn(3000).delay(2000).fadeOut(2000)
            }
            else {
                myChart.data.labels = res.unitHeadInformation
                myChart.data.datasets[0].data = res.unitHeadSpecificTargets
                myChart.data.datasets[1].data = res.myGrossMargin
                myChart.update()

                markUpChart.data.labels = res.unitHeadInformation
                markUpChart.data.datasets[0].label = `${res.selectedMonth} Markup`
                markUpChart.data.datasets[0].data = res.unitHeadCurrentMarkUp
                markUpChart.update()

                numberOfTripsChart.data.labels = res.unitHeadInformation 
                numberOfTripsChart.data.datasets[0].data = res.tripCount
                numberOfTripsChart.data.datasets[1].data = res.monthlyTripRemainder
                numberOfTripsChart.update()

                transporterChart.data.labels = res.unitHeadInformation 
                transporterChart.data.datasets[0].label = `Transporter Gained for ${res.selectedMonth}`
                transporterChart.data.datasets[0].data = res.transportersGained
                transporterChart.update()

                clientExpectedTripsChart.data.labels = res.nameOfClient
                clientExpectedTripsChart.data.datasets[0].data = res.tripDoneForClient
                clientExpectedTripsChart.data.datasets[1].data = res.tripRemainder
                clientExpectedTripsChart.update()
                clientExpectedTripsChart.options.title.text = `Client trip count for ${res.selectedMonth}`

                bonusBarChart.data.labels = res.unitHeadInformation 
                bonusBarChart.data.datasets[0].data = res.totalBonus
                bonusBarChart.update()

                $('#loader').html('')
            }
        })
    })

    $(document).on('click', '#filterDateRange', function() {
        $userid = $('#userSelectedId').val();
        $dateRangeFrom = $('#drFrom').val();
        $dateRangeTo = $('#drTo').val();
        $('#tripsDateRangeFilter').html('<i class="icon-spinner2 spinner"></i>Please wait...')
        $.get('/buh-trips-breakdown', {user_id: $userid, date_from: $dateRangeFrom, date_to: $dateRangeTo }, function(data){
            $('#tripsDateRangeFilter').html(data)
        })
    })

    $('#shootStatAnalysis').click(function() {
        $datedFrom = $('#datedFrom').val()
        if($datedFrom === '') {
            $('#datedFrom').focus()
            return false
        }
        $datedTo = $('#datedTo').val();
        if($datedTo === '') {
            $('#datedTo').focus()
            return false
        }
        $.get('/performance-analysis', {datedFrom: $datedFrom, datedTo: $datedTo }, function(data) {
            $('#performanceAnalysis').html(data).addClass('mb-3 ml-3 animate__zoomIn')
            $('#closePerformanceAnalysis').removeClass('d-none')
        })
    })

    $('#closePerformanceAnalysis').click(function() {
        window.location.href='';
    })


    


</script>






@stop

