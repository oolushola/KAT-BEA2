@extends('layout')

@section('title') Receivables Tracker @stop

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
@stop

@section('main')
 <!-- Page header -->
 <div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4 class="bg-warning p-2"><span class="font-weight-semibold">Invoices Due Date Today</span> - {{ $overdueTodayCounter }}</h4>
            <h4 class="bg-danger p-2"><i class="ml-2 mr-2"></i> <span class="font-weight-semibold">Overdue Invoices</span> - {{ $overdueCounter }}</h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                <a href="#" class="btn btn-link btn-float text-default">
                    <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-danger font-weight-bold">{{ count($totalUnpaidTrips) }}</h2>
                    <p class="text-primary font-weight-bold">Total Unpaid Trips</p>
                </a>
                <a href="#truckAvailability" data-toggle="modal"  class="btn btn-link btn-float text-default">
                    <h2 style="margin:0; padding:10px; letter-spacing:10px;" class="bg-primary font-weight-bold">{{ count($invoicesYetToBePaid) }}</h2>
                    <p class="text-primary font-weight-bold">Total Unpaid Invoices</p>
                </a>
            </div>
        </div>
    </div>

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb font-weight-bold">
                <a href="#" class="breadcrumb-item"><i class="icon-home2 mr-2"></i>Average Margin(Last Month) </a>
                <span class="breadcrumb-item active">&#x20a6;{{ $avgMarginForLastMonth }}</span>
            </div>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="breadcrumb justify-content-center mr-3 font-weight-bold">
                <a href="{{URL('view-trip-thread')}}" class="breadcrumb-elements-item">
                    <i class="icon-coins mr-2"></i>
                    Receivables Account: &#x20a6;{{ number_format($receiveablesAccount[0]->receivable * 1.025, 2) }}
                </a>
            </div>
            <div class="breadcrumb justify-content-center text-danger font-weight-bold">
                <a href="#" class="breadcrumb-elements-item">
                    <i class="icon-piggy-bank mr-1 text-danger"></i>
                    Amount of Overdue Invoices: &#x20a6; {{ number_format($sumTotalofOverdueInvoices * 1.025, 2) }}
                </a>
            </div>
            
        </div>
    </div>

    
</div>

<!-- /page header -->

<div class="row mt-3">
    <div class="col-md-3 mb-2">
        <div class="dashboardbox text-center">
            <!-- <canvas id="gaugeChart"  height="200"></canvas> -->
            <div id="chart_div" style="height:120px; margin: 0 auto; position:relative; left:60px"></div>
            <span class="badge badge-primary mb-1">Total Margin</span>
            <h5 class="font-weight-bold text-primary mb-2">&#x20a6;{{ number_format($totalMargin, 2) }} </h5>

            <span class="badge badge-danger">Other Expenses</span>
            <h5 class="font-weight-bold text-danger mb-2">&#x20a6;{{ number_format($totalExpenses, 2) }} </h5>

            <span class="badge badge-danger">Profit / Loss</span>
            <?php $profit_and_loss = $totalMargin - $totalExpenses; ?>
            @if($profit_and_loss <= 0)
            <h5 class="font-weight-bold text-danger mb-2">&#x20a6; {{ number_format(($profit_and_loss) ,2)}} </h5>
            @else
            <h5 class="font-weight-bold text-info mb-2">&#x20a6; {{ number_format(($profit_and_loss) ,2)}} </h5>
            @endif

            <span class="badge badge-primary">Average Account Receivables</span>
            <h2 class="font-weight-bold mb-2">18 Days</h2>
            
        </div>
    </div>
    
    <div class="col-md-9 col-sm-12 mb-2">
        <div class="dashboardbox">
            <canvas id="marginByMonthStack" height="130" data-toggle="modal" href=".expensesBreakdown"></canvas>
        </div>
    </div>

</div>

<div class="row mt-3">
    <div class="col-md-6 mb-2">
        <div class="dashboardbox">
            <span>
                <select id="sortClientRevenue" style="font-size:11px; padding:2px; outline:none; width:110px; border: none">
                    <option value="">All Revenue</option>
                    @foreach($clients as $client) 
                        <option value="{{$client->id}}">{{ ucwords($client->company_name) }}</option>
                    @endforeach
                </select>
                <span id="revenueLoader"></span>
            </span>
            <canvas id="revenueByMonth"  height="200"></canvas>
        </div>
    </div>

    <div class="col-md-6 col-sm-12 mb-2" id="">
        <div class="dashboardbox">
            <canvas id="receivablesStack" height="200" data-toggle="modal" href=".paymentModel"></canvas>
        </div>
    </div>
</div>

@include('finance.financials._payment-model')
@include('finance.financials._expenses-breakdown')

@stop

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    $(function() {
        var ctx = document.getElementById('marginByMonthStack');
        var periods = <?php echo json_encode($periods); ?>;
        var margins = <?php echo json_encode($margins); ?>;
        var expenses = <?php echo json_encode($monthlyExpenses); ?>;
        var profitAndLoss = <?php echo json_encode($profitAndLoss); ?>;
        var marginExpensesProfitAndLossData = {
                labels: periods,
                datasets: [
                    
                    {
                        label: 'Margin',
                        data: margins,
                        backgroundColor: '#150e06',
                        borderWidth: 1
                    },
                    {
                        label: 'Other Expenses',
                        data: expenses,
                        backgroundColor: 'red',
                        borderWidth: 1
                    },
                    {
                        label: 'Profit/Loss',
                        data: profitAndLoss,
                        backgroundColor: '#ccc',
                        borderWidth: 1
                    }
                    
                ]
            }


        var marginExpenseProfitAndLossChart = new Chart(ctx, {
            type: 'bar',
            data: marginExpensesProfitAndLossData,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                    display: true
                },
                
                title: {
                    display: true,
                    text: 'Margin by Month (₦) - Gate Outs',
                    position: 'bottom'
                },
                onClick: function(c, i) {
                    e = i[0];
                    if(e) {
                        var xValue = this.data.labels[e._index];
                        var yValue = this.data.datasets[0].data[e._index];
                        const date = new Date();
                        const fullYear = date.getFullYear();

                        if(fullYear === Number(xValue.split(',')[1])) {
                            $.get('/expenses-breakdown', { time_inview: xValue }, function(data) {
                                $('#expensesModelInfo').html('Expenses for '+xValue)
                                expensesChart.data.labels = data.categories;
                                expensesChart.data.datasets[0].data = data.percentage
                                expensesChart.update()
                            })
                        }
                    }
                }
            },
            
        });

        //revenue generated per month
        var periods = <?php echo json_encode($periods);  ?>;
        var revenues = <?php echo json_encode($revenues);  ?>;
        
        var ctx = document.getElementById('revenueByMonth');
        var myClientRevenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: periods,
                datasets: [
                    {
                        label: 'Revenue',
                        data: revenues,
                        backgroundColor: '#150e06',
                        borderWidth: 1
                    },

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
                "hover": {
                    "animationDuration": 0
                },
                "animation": {
                    "duration": 1,
                    "onComplete": function() {
                        var chartInstance = this.chart,
                        ctx = chartInstance.ctx

                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                        ctx.textAlign = 'center'
                        ctx.textBaseline = 'bottom'

                        this.data.datasets.forEach(function(dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i)
                            meta.data.forEach(function (bar, index) {
                                var data = dataset.data[index]
                                ctx.fillText(data, bar._model.x, bar._model.y, 22)
                            })
                        })
                    }
                },
                title: {
                    display: true,
                    position: 'bottom',
                    text: 'Revenue by Month(₦) - Gate Outs '
                }
            },
        });

        //For client Measureables
        var companyNames = <?php echo json_encode($companyNames); ?>;
        var yetToDueDifference = <?php echo json_encode($yetToDueDifference); ?>;
        var sumOverdue = <?php echo json_encode($sumOverdue); ?>;

        var ctx = document.getElementById('receivablesStack');
        var myChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: companyNames,
                datasets: [
                    
                    {
                        label: 'Value of Undue Invoice',
                        data: yetToDueDifference,
                        backgroundColor: '#73c2fb',
                        borderWidth: 1
                    },
                    {
                        label: 'Overdue',
                        data: sumOverdue,
                        backgroundColor: '#ff6347',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'bottom',
                    display: true
                },
                scales: {
                    yAxes: [{
                        stacked: true
                    }],
                    xAxes: [{
                        stacked: true
                    }]
                },
                title: {
                    text: 'Client Outstanding Payments (₦)',
                    position: 'top',
                    display: true
                },
                onClick: function(c, i) {
                    e = i[0];
                    if(e) {
                        var xValue = this.data.labels[e._index];
                        var yValue = this.data.datasets[0].data[e._index];
                        //$('#paymentStatistics').html('<i class="icon-spinner3 spinner"></i>Please wait, fetching client payments. This might take some time.')
                        $('#selectedClient').html(xValue)
                        $.get('/client-payment-model', { client: xValue }, function(data) {
                            $('#selectedClient').html(data.clientInfo.company_name+`<span class=" text-center ml-1 text-danger font-weight-semibold font-size-sm"> ${data.avg_payment_days} days payment expectation</span>`);
                            $('#paymentStatistics').html(data.model)
                            
                            noOfDaysTaken.data.labels = data.invoiceNos
                            noOfDaysTaken.data.datasets[0].data = data.no_days_paid
                            noOfDaysTaken.update()
                            
                        })
                    }
                    else{
                        return false;
                    }
                }
            },
        });


        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var overduecount = <?php echo json_encode($overdueCounter); ?>;
            var totalUnpaidInvoices = <?php echo json_encode(count($invoicesYetToBePaid)); ?>;
            var overduePercentage = overduecount / totalUnpaidInvoices * 100;
            
            var data = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['Overdue', overduePercentage],
            ]);

            var options = {
            width: 1000, height: 120,
            redFrom: 50, redTo: 100,
            yellowFrom:20, yellowTo: 50,
            minorTicks: 5
            };

            var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
            chart.draw(data, options);
        }

        $('#sortClientRevenue').change(function() {
            $client = $(this).val();
            $('#revenueLoader').html('<span class="font-size-sm"><i class="icon-spinner3 spinner"></i>Please wait while we generate report</span>')
            $.get('/client-revenue', {client: $client}, function(data) {
                if($client == 0) {
                    myClientRevenueChart.data.labels = periods;
                    myClientRevenueChart.data.datasets[0].data = revenues;
                }
                else {
                    myClientRevenueChart.data.labels = data[0];
                    myClientRevenueChart.data.datasets[0].data = data[1];
                }
                myClientRevenueChart.update();
                $('#revenueLoader').html('')
            }) 
        })

        var paymentTrajectory = document.getElementById('paymentTrajectory')
        var noOfDaysTaken = new Chart(paymentTrajectory, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'No of Days Taken',
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
                },
            }
        });

        $(document).on('change', '#filterPayments', function() {
            var value = $(this).val().toLowerCase();
            $(`#paymentsLog .col-md-2`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        })

        $(document).on("keyup", '#searchFilteredPayments', function() {
            var value = $(this).val().toLowerCase();
            $(`#paymentsLog .col-md-2`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        var expensesCtx = document.getElementById('expensesModel');
        var expensesChart = new Chart(expensesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Staff Cost', 'Finance', 'Frieght Charges', 'Service Charge'],
                datasets: [{
                    label: '# of Votes',
                    data: [10, 5, 6, 7],
                    backgroundColor: [
                        '#ff7675', 
                        '#e17055',
                        '#fd79a8',
                        '#636e72',
                        '#2d3436',
                        '#00b894',
                        '#a29bfe'
                    ],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            
        });



    });
</script>
@stop