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
                    <!-- <a href="#" class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-danger font-weight-bold">100</h2>
                        <p class="text-primary font-weight-bold">TOTAL GATE OUT</p>
                    </a> -->
                    <!-- <a href="#truckAvailability" data-toggle="modal"  class="btn btn-link btn-float text-default">
                        <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-primary font-weight-bold">{{ 100 }}</h2>
                        <p class="text-primary font-weight-bold">MY APRIL TRIPS</p>
                    </a> -->
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
                    <span class="breadcrumb-item active"> For the Month of : {{ date('F') }}</span>
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
            <!-- <div class="col-md-8">
                <canvas id="stackedBar" height="200"></canvas>
            </div> -->
        </div>
    </div>


   

    <?php //echo json_encode($unitHeadInformation); json_encode($unitHeadSpecificTargets); json_encode($myGrossMargin);json_encode($myOutstanding); json_encode($unitHeadCurrentMarkUp);  ?><br>
    

</div>

@stop


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script>
    var unitHeadInformations = <?php echo json_encode($unitHeadInformation); ?>;
    var expectedMargin = <?php echo json_encode($unitHeadSpecificTargets); ?>;
    var achieved = <?php echo json_encode($myGrossMargin); ?>;
    var deficit = <?php echo json_encode($myOutstanding); ?>;
    var currentMonthMarkup = <?php echo json_encode($unitHeadCurrentMarkUp); ?>;

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
                },
                {
                    label: 'Outstanding',
                    data: deficit,
                    backgroundColor: 'red',
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
                            return '#' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            } else {
                            return '#' + value;
                            }
                        }   

                    }
                }]
            },
        },
    });
    

    myBarChart('bar', 'buhCurrentMonthMargin', unitHeadInformations, 'Current Markup', currentMonthMarkup)
    
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
                            beginAtZero: true
                        }
                    }]
                },
            },
        });
    } 


        

   
</script>






@stop

