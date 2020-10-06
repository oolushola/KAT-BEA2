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
            <div class="col-md-6">
                <canvas id="numbersForTheMonth" height="120" data-toggle="modal" href="#specificBuh"></canvas>
            </div>
        </div>
    </div>
    
</div>

@include('performance-metric.partials._specificNumberPerformance')

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
                label: 'My Daily Gate out for April, 2020',
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
            datasets: [{
                label: 'Number of Trips',
                data: tripCount,
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
                //myChart.data.datasets[2].data = res.myOutstanding
                myChart.update()

                markUpChart.data.labels = res.unitHeadInformation
                markUpChart.data.datasets[0].label = `${res.selectedMonth} Markup`
                markUpChart.data.datasets[0].data = res.unitHeadCurrentMarkUp
                markUpChart.update()

                numberOfTripsChart.data.labels = res.unitHeadInformation
                numberOfTripsChart.data.datasets[0].data = res.tripCount
                numberOfTripsChart.update()

                $('#loader').html('<i class="icon-checkmark2"></i>Touché, Completed.').addClass('mt-2 font-size-xs font-weight-semibold').fadeIn(3000).delay(2000).fadeOut(2000)
            }
        })
    })

    $(document).on('keyup', '#searchPreviousTripsOfSelectedDate', function() {
        $value = $(this).val().toLowerCase()
        $(`#selectedDateDataRecord tr`).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf($value) > -1)
        });
    })


    //






   
</script>






@stop

