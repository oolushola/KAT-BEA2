@extends('layout')

@section('title') Financial Reporting @stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
@stop

@section('main')

<div class="main-gallery">
    <div class="gallery-cell">
        <div class="card" id="waybillStatus" value="1">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold label">WAYBILL STATUS</p>
                        </div>
                        <div class="flip-card-back bg-danger">
                            <span class="icon-books" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="gallery-cell">
        <div class="card" id="unpaidInvoices" value="0">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold label">UNPAID INVOICES</p>
                        </div>
                        <div class="flip-card-back bg-info">
                            <span class="icon-coins" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="gallery-cell">
        <div class="card" id="paidInvoices" value="0">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold">PAID INVOICES</p>
                        </div>
                        <div class="flip-card-back bg-success">
                            <span class="icon-wallet" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="gallery-cell">
        <div class="card" id="uninvoicedTrips" value="0">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold">UNINVOICED TRIPS</p>
                        </div>
                        <div class="flip-card-back">
                            <span class="icon-infinite" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="gallery-cell">
        <div class="card" id="invoicedTrips" value="0">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold">INVOICED TRIPS</p>
                        </div>
                        <div class="flip-card-back bg-primary">
                            <span class="icon-checkmark2" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="gallery-cell">
        <div class="card" id="transporterAccount" value="0">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold">TRANSPORTER ACCOUNT</p>
                        </div>
                        <div class="flip-card-back bg-info">
                            <span class="icon-train" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="gallery-cell">
        <div class="card" id="outstandingBills" value="1">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold">OUTSTANDING BILLS</p>
                        </div>
                        <div class="flip-card-back bg-danger">
                            <span class="icon-piggy-bank" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
    <div class="gallery-cell">
        <div class="card" id="trips" value="0">
            <div class="card-body">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <p class="mt-4 font-weight-bold">TRIPS</p>
                        </div>
                        <div class="flip-card-back bg-success">
                            <span class="icon-bus" style="font-size:100px"</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-7 searchBreakdown unpaidInvoices d-none font-weight-bold font-size-xs">
        Filter by client
        <select class="finance-report__select" id="client">
            <option value="0">Choose Client</option>
            <option value="all">ALL</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
            @endforeach
        </select>
        <button id="runUnpaidInvoices" class="uni">SHOOT <i class="icon-paperplane"></i></button>
    </div>

    <div class="col-md-7 searchBreakdown paidInvoices d-none font-weight-bold font-size-xs">
        <span>From: <input type="date" id="piDateFrom" class="finance-report__input"></span>
        <span>To: <input type="date" id="piDateTo" class="finance-report__input"></span>
        <button id="runPaidInvoices" class="pi">SHOOT <i class="icon-paperplane"></i></button>
    </div>

    <div class="col-md-7 searchBreakdown uninvoicedTrips d-none font-weight-bold font-size-xs">
        Filter by Status
        <select class="finance-report__input" id="tracker">
            <option value="">Choose</option>
            <option value="all">All Uninvoiced</option>
            <option value="6">On Journey</option>
            <option value="7">Arrived Destination</option>
            <option value="8">Offloaded</option>
        </select>
        <button id="runUninvoicedTrips" class="stat">SHOOT <i class="icon-paperplane"></i></button>
    </div>

    <div class="col-md-7 searchBreakdown invoicedTrips d-none font-weight-bold font-size-xs">
        <select class="finance-report__select" id="clientInvoiced">
            <option value="0">Choose Client</option>
            @foreach($clients as $client)
            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
            @endforeach
        </select>
        <span>From: <input type="date" id="invDateFrom" class="finance-report__input"></span>
        <span>To: <input type="date" id="invDateTo" class="finance-report__input"></span>
        <button id="runInvoicedTrips" class="invd">SHOOT <i class="icon-paperplane"></i></button>
    </div>

    <div class="col-md-7 searchBreakdown transporterAccount d-none font-weight-bold font-size-xs">
        <select class="finance-report__input" id="transporter">
            <option value="0">Choose Transporter</option>
            @foreach($transporters as $transporter)
            <option value="{{ $transporter->id }}">{{ $transporter->transporter_name }}</option>
            @endforeach
        </select>
        <span>From: <input type="date" id="transporterDateFrom" class="finance-report__input"></span>
        <span>To: <input type="date" id="transporterDateTo" class="finance-report__input"  id=""></span>
        <button id="runTransporterAccount" class="transporterAcc">SHOOT <i class="icon-paperplane"></i></button>
    </div>

    <div class="col-md-7 searchBreakdown trips d-none font-weight-bold font-size-xs">
        TRIP ID LOOK UP <input type="text" id="search" class="finance-report__input" />
        <button id="runTripSearch" class="tripSearch">SHOOT <i class="icon-paperplane"></i></button>
    </div>
    
    <div class="col-md-5">
        <div id="validator"></div>
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
<script type="text/javascript" src="{{ URL::asset('js/validator/finance-reporting.js')}}"></script>
<script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>

@stop

