@extends('layout')

@section('title')Financials ::. Dashboard @stop

@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/css/custom.css')}}">
@stop

@section('main')

<!-- Main content -->
    
<div class="content-wrapper">
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
                <div class="d-flex justify-content-center">
                    <a href="{{URL('invoices')}}" class="btn btn-link btn-float text-default">
                        <i class="icon-calculator text-primary"></i> 
                        <span>Receivables Tracker</span>
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
        <div class="col-md-7 col-sm-6">
            <div class="card mb- mt-1">
                <canvas id="cashInflow" height="300"></canvas>
            </div>
        </div>
        <div class="col-md-5 mb-4 col-sm-6">
            <div class="card mt-1 ml-2 mr-2">
                <canvas id="cashCycle" height="250"></canvas>
                <div class="row m-0">
                    <section class="col-md-6 font-size-sm font-weight-semibold mt-2 text-center" style="background:#FFCCCC">
                        <span class="d-block mt-2 mb-2">&#8358;{{ $invoicedNotPaid }}M</span>
                    </section>
                    <section class="col-md-6 font-size-sm font-weight-semibold mt-2 text-center" style="background: #C56CF0">
                        <span class="d-block mt-2 mb-2">&#8358;{{ $notInvoiced }}M</span>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="card mb- mt-1">
                <canvas id="adminMktOpx" height="250"></canvas>
            </div>
        </div>
        <div class="col-md-8 mb-4 col-sm-6">
            <div class="card mt-1 ml-2 mr-2">
                <canvas id="revenueYearOnYear" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-6">
            <div class="row">
                <section class="col-md-6">
                    <div class="card mt-1">
                        <canvas id="grossProfit" height="250"></canvas>
                    </div>
                </section>
                
                <section class="col-md-6">
                    <div class="card mt-1">
                        <canvas id="ebitda" height="250"></canvas>
                    </div>
                </section>
            </div>
        </div>
        <div class="col-md-6 mb-4 col-sm-6">
            <div class="card mt-1 ml-2 mr-2">
                <canvas id="debtAndEquityRatio" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4 col-sm-6">
            <div class="card mt-1 ml-2 mr-2">
                <canvas id="avgInvoiceAmntTrend" height="250"></canvas>
            </div>
        </div>
    </div>

</div>
<!-- /main content -->

@stop


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<!-- Finance projections -->
<script>
    let months = <?php echo json_encode($monthsOfTheYear); ?>;
    let lastYearRevenue = <?php echo json_encode($lastYearRevenue); ?>;
    let currentYearRevenue = <?php echo json_encode($currentYearRevenue); ?>;
    $invoiceNotPaid = <?php echo json_encode($invoicedNotPaid); ?>;
    $notInvoiced = <?php echo json_encode($notInvoiced); ?>;
    $cashInflow = <?php echo json_encode($totalCis); ?>;
    $cashOutflow = <?php echo json_encode($outflow); ?>;
    $currentInvTrend = <?php echo json_encode($invTrend); ?>;
    $lastYearInvTrend = <?php echo json_encode($previousYearInvTrend); ?>;

    console.log($currentInvTrend);
    var cashInflow = document.getElementById('cashInflow');
    var mixedChart = new Chart(cashInflow, {
        type: 'line',
        data: {
            datasets: [{
                
                label: 'Cash Inflow',
                data: $cashInflow,
                fill: false,
                borderColor: [
                    '#C56CF0',
                    // 'rbg(0, 0, 255)'
                ]
            }, {
                label: 'Cash Outflow',
                data: $cashOutflow,
                // fill: false,
                borderColor: [
                    // 'rgba(0, 0, 0, 0.3)'
                    'red'
                ],
                // Changes this dataset to become a line
                type: 'line'
            }],
            labels: months
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                    datalabels: {
                        color: '#333',
                    }
                }
        }
    });

    var cashCycle = document.getElementById('cashCycle');
    var cashCycleChart = new Chart(cashCycle, {
        type: 'doughnut',
        data: {
            labels: ['Invoiced Not Paid', 'Not Invoiced'],
            datasets: [{
                label: 'Cash Cycle ',
                data: [$invoiceNotPaid, $notInvoiced],
                backgroundColor: [
                    '#FFCCCC',
                    '#C56CF0'
                ],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Cash Cycle (₦)',
                position: 'top'
            },
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        let percentage = (value*100 / sum).toFixed(2)+"%";
                        return percentage;
                    },
                    color: '#333',
                }
            },
            onClick: function(c, i) {
                e = i[0];
                var xValue = this.data.labels[e._index];
                var yValue = this.data.datasets[0].data[e._index];
                console.log(xValue)
            }
        }
    });

    var adminMktOpex = document.getElementById('adminMktOpx');
    var adminMktOpexChart = new Chart(adminMktOpex, {
        type: 'bar',
        data: {
            labels: ['Admin', 'Mkt', 'Opex'],
            datasets: [{
                label: 'Admin, MKT, Opex',
                data: [98, 106, 89],
                backgroundColor: [
                    '#FFCCCC',
                    '#C56CF0'
                ],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Admin, Market and Opex',
                position: 'top'
            },
            plugins: {
                datalabels: {
                    color: '#333',
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
        }
    });

    var yoy = document.getElementById('revenueYearOnYear');
    var yoyChart = new Chart(yoy, {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Revenue, 2020',
                data: lastYearRevenue,
                backgroundColor: 'rgba(153, 102, 255, 0.2)'
            }, {
                label: 'Revenue, 2021',
                data: currentYearRevenue,
                fill: false,
                borderColor:'green',
                type: 'line'
            }],
            labels: months
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                datalabels: {
                    color: '#333',
                }
            },
            title: {
                display: true,
                text: 'Year on Year Revenue (₦)',
                position: 'top'
            }
        }
    });

    var ebitda = document.getElementById('ebitda');
    var ebitdaChart = new Chart(ebitda, {
        type: 'doughnut',
        data: {
            labels: ['E', 'I', 'T', 'D'],
            datasets: [{
                label: 'Cash Cycle ',
                data: [8, 5, 9, 4],
                backgroundColor: [
                    '#FFCCCC',
                    '#C56CF0'
                ],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                display: true,
                text: 'EBITDA (₦)',
                position: 'top'
            },
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        let percentage = (value*100 / sum).toFixed(2)+"%";
                        return percentage;
                    },
                    color: '#333',
                }
            }
        }
    });

    var grossProfit = document.getElementById('grossProfit');
    var grossProfitChart = new Chart(grossProfit, {
        type: 'doughnut',
        data: {
            labels: ['Expected', 'Actual'],
            datasets: [{
                label: '',
                data: [8, 5],
                backgroundColor: [
                    'Red',
                    '#C56CF0'
                ],
                borderWidth: 1
            }]
        },
        options: {
            title: {
                display: true,
                text: 'Gross Profit (₦)',
                position: 'top'
            },
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => {
                            sum += data;
                        });
                        let percentage = (value*100 / sum).toFixed(2)+"%";
                        return percentage;
                    },
                    color: '#fff',
                }
            }
        }
    });

    //debtAndEquityRatio
    var debtAndEquityRatio = document.getElementById('debtAndEquityRatio');
    var debtAndEquityRatioChart = new Chart(debtAndEquityRatio, {
        type: 'line',
        data: {
            datasets: [{
                label: 'DER 2019',
                data: [9, 25, 18, 16, 22, 36, 18, 40, 17, 19, 22, 40],
                backgroundColor: 'rgba(153, 102, 255, 0.1)'
            }, {
                label: 'DER, 2020',
                data: [18, 22, 34, 19, 16, 28, 12.7, 35.7, 28.8, 10, 18, 28],
                fill: false,
                borderColor:'red',
                type: 'line'
            }],
            labels: months
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                datalabels: {
                    color: '#333',
                }
            },
            title: {
                display: true,
                text: 'Debt & Equity Ratio (₦)',
                position: 'top'
            }
        }
    });

    //Average Margin of Invoice
    var avgInvoiceAmntTrend = document.getElementById('avgInvoiceAmntTrend');
    var avgInvoiceAmntChart = new Chart(avgInvoiceAmntTrend, {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Avg. Invoiced Trend 2020',
                data: $lastYearInvTrend,
                backgroundColor: 'rgba(153, 102, 255, 0.1)'
            }, {
                label: 'Avg. Invoiced Trend, 2021',
                data: $currentInvTrend,
                fill: false,
                borderColor:'red',
                type: 'line'
            }],
            labels: months
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                datalabels: {
                    color: '#333',
                }
            },
            title: {
                display: true,
                text: 'Average Invoice Trend (₦)',
                position: 'top'
            }, 
        }
    });

</script>
@stop