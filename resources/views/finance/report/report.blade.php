@extends('layout')

@section('title') Financial Reporting @stop

@section('main')

<div class="row">
    <div class="col-md-2 col-sm-6 col-xs-12 pointer">
        <div class="card" id="waybillStatus">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <h5 class="mt-4 font-weight-bold label">WAYBILL STATUS</h5>
                        </div>
                        <div class="flip-card-back bg-danger">
                            <span class="icon-books" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12 pointer">
        <div class="card" id="unpaidInvoices">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <h5 class="mt-4 font-weight-bold label">UNPAID INVOICES</h5>
                        </div>
                        <div class="flip-card-back bg-info">
                            <span class="icon-coins" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 col-xs-12 pointer">
        <div class="card" id="paidInvoices">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <h5 class="mt-4 font-weight-bold">PAID INVOICES</h5>
                        </div>
                        <div class="flip-card-back bg-success">
                            <span class="icon-wallet" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 col-xs-12 pointer">
        <div class="card" id="uninvoicedTrips">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <h5 class="mt-4 font-weight-bold">UNINVOICED TRIPS</h5>
                        </div>
                        <div class="flip-card-back">
                            <span class="icon-infinite" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 col-xs-12 pointer">
        <div class="card" id="invoicedTrips">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <h5 class="mt-4 font-weight-bold">INVOICED TRIPS</h5>
                        </div>
                        <div class="flip-card-back bg-primary">
                            <span class="icon-checkmark2" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive" id="reporting"></div>
    </div>
</div>

@stop


@section('script')
<script type="text/javascript" src="{{URL::asset('/js/validator/excelme.js')}}"></script>
<script>
    $(function() {
        reporting('#waybillStatus', 'waybill-status?v=Waybill Status')
        reporting('#unpaidInvoices', '/unpaid-invoices?v=Unpaid Invoices')
        reporting('#paidInvoices', '/paid-invoices?v=Paid Invoices')
        reporting('#uninvoicedTrips', '/uninvoiced-trips?v=Uninvoiced Trips')
        reporting('#invoicedTrips', '/invoiced-trips?v=Invoiced Trips')

        function reporting(specific, url) {
            $(specific).click(function() {
                $('.card').removeClass('bg-primary-400')
                $(this).addClass('bg-primary-400')
                $('#reporting').html('<i class="spinner icon-spinner3"></i>Please wait...').addClass('font-weight-semibold')
                $.get(`/finance/${url}`, function(data) {
                    $('#reporting').html(data)
                })
            })
        }

        $(document).on('click', '#exportBtn', function(e)
        {
            var name = Math.random().toString().substring(7);
            $("#reporting").table2excel({
                filename:`Report-${name}.xls`
            });
        })    
    })
    

</script>
@stop

