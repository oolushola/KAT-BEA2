@extends('layout')
@section('title') Kayapay::Dashboard @stop

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

<div class="content-wrapper">
    <!-- Page header -->
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Kaya Pay</span> - Dashboard</h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>
        </div>
        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">
                <div class="breadcrumb">
                    <a href="index.html" class="breadcrumb-item"><i class="icon-home2 mr-2"></i>Home - Kaya Pay</a>
                    <span class="breadcrumb-item active">Dashboard</span>
                </div>
                <div class="breadcrumb justify-content-center text-danger ml-4">
                    <a href=".quickTripFinder" id="quickTripFinder" class="breadcrumb-elements-item font-weight-semibold text-primary" data-toggle="modal">
                        <i class="icon-search4  text-danger"></i>
                        SEARCH
                    </a>
                </div>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">
                <div class="breadcrumb justify-content-center text-danger">
                    <a href="#" class="breadcrumb-elements-item">
                        <i class="icon-flag4 mr-1 text-danger"></i>
                        DELETED TRIPS
                    </a>
                </div>
                
            </div>
        </div>
    </div>
    <!-- /page header -->
    

    <div class="row mt-3">
        <div class="col-md-4 col-sm-12 mb-2" id="gatedOutToday">
          <div class="dashboardbox">
              <canvas id="gatedOutTodayChart" height="200" data-toggle="modal" href=".gatedOutTodayChart"></canvas>
          </div>
        </div>
        <div class="col-md-4 mb-2" data-toggle="modal" href="#dueToday" id="dueToday">
            <div class="dashboardbox">
                <canvas id="dueTodayChart"  height="200"></canvas>
            </div>
        </div>
        <div class="col-md-4 mb-2" data-toggle="modal" href="#overdue" id="overdue">
            <div class="dashboardbox">
                <canvas id="overdueChart"  height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4 mb-2" data-toggle="modal" href="#financeBreakdown" id="financeBreakdown">
            <div class="dashboardbox">
                <canvas id="financeBreakdownChart"  height="200"></canvas>
            </div>
        </div>
        <div class="col-md-8 col-sm-12 mb-2" id="">
          <div class="dashboardbox">
            <canvas id="dailyGateOutChart" height="130" data-toggle="modal" href=".dailyGateOutChart"></canvas>
          </div>
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
    $timing = sha1(Date('H:i:s A'));
?>

@stop

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
<script type="text/javascript">
  const drawChart = (type, targetId, labels, label, data, bgColor) => {
    var ctx = document.getElementById(targetId).getContext('2d');
    var ctxChart = new Chart(ctx, {
      type: type,
      data: {
          labels: labels,
          datasets: [{
              label: label,
              data: data,
              backgroundColor: bgColor,
              borderColor: '#DF2357',
              borderWidth: 0
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
    return ctxChart;
  };

  const countTripsDisbursement = <?php echo json_encode(count($tripsDisbursedToday)); ?>;  
  drawChart(
    'bar', 
    'gatedOutTodayChart', 
    ['Trip\'s disbursed for, today'], 
    new Date().toLocaleDateString(),
    [countTripsDisbursement],
    '#FF8A01'
  );
  
  const tripsDueToday = <?php echo json_encode(count($tripsDueToday)); ?>;  
  drawChart(
    'bar', 
    'dueTodayChart', 
    ['Due Today'], 
    new Date().toLocaleDateString(),
    [tripsDueToday], 
    '#FF8A01'
  );

  const tripsOverdue = <?php echo json_encode(count($tripsOverdueToday)); ?>;  
  drawChart(
    'bar', 
    'overdueChart', 
    ['Overdue for repayment'], 
    new Date().toLocaleDateString(), 
    [tripsOverdue], 
    '#FF8A01'
  );

  const financeProjected = <?php echo json_encode($financeProjections); ?>;  
  drawChart(
    'bar', 
    'financeBreakdownChart', 
    ['Finance Cost', 'Finance Income', 'Net Margin'], 
    'Expense Breakdown (₦)',
    financeProjected, 
    ['red', '#FF8A01', 'rgba(255, 138, 1, .6)']
  );

  const days = <?php echo json_encode($daysIntheMonth);  ?>;
  const tripsDibursedFor = <?php echo json_encode($disbursedFor);  ?>;

  const tripsDisbursedForChart = drawChart(
    'bar',
    'dailyGateOutChart',
    days,
    'Daily disbursement',
    tripsDibursedFor,
    'rgba(255, 138, 1, .6)'
  )
  tripsDisbursedForChart.options = {
    onClick: function(c, i) {
      e = i[0];
      var xValue = this.data.labels[e._index];
      console.log(xValue)
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
          ctx.textBaseline = 'center'

          this.data.datasets.forEach(function(dataset, i) {
              var meta = chartInstance.controller.getDatasetMeta(i)
              meta.data.forEach(function (bar, index) {
                  var data = dataset.data[index]
                  ctx.fillText(data, bar._model.x, bar._model.y, 35)
              })
          })
      }
    },
  }

</script>
@stop
