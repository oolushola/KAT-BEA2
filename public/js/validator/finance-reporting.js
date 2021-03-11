$(function() {
    $('.main-gallery').flickity({
        cellAlign: 'left',
        contain: true
    });

    reporting('#waybillStatus', 'waybill-status?v=Waybill Status')
    reporting('#unpaidInvoices', '', '.unpaidInvoices')
    reporting('#paidInvoices', '', '.paidInvoices')
    reporting('#uninvoicedTrips', '', '.uninvoicedTrips')
    reporting('#invoicedTrips', '', '.invoicedTrips')
    reporting('#transporterAccount', '', '.transporterAccount')
    reporting('#outstandingBills', 'outstanding-bills?v=Outstanding Bills', '.outstandingBills')
    reporting('#trips', '', '.trips')
    
    function reporting(specific, url, showBreakdown) {
        $(specific).click(function() {
            $('.card').removeClass('bg-primary-400')
            $('.searchBreakdown').addClass('d-none')
            $(showBreakdown).removeClass('d-none')
            $(this).addClass('bg-primary-400')
            $value = $(this).attr('value')
            if($value == 0) {
                $('#reporting').html('')
                return false;
            }
            submit(url)
        })
    }

    oneStepAheadReport('#runUnpaidInvoices', 'unpaid-invoices?v=Unpaid Invoices')
    oneStepAheadReport('#runPaidInvoices', 'paid-invoices?v=Paid Invoices')
    oneStepAheadReport('#runUninvoicedTrips', 'uninvoiced-trips?v=Uninvoiced Trips')
    oneStepAheadReport('#runInvoicedTrips', 'invoiced-trips?v=Invoiced Trips')
    oneStepAheadReport('#runTransporterAccount', 'transporter-account?v=Transporter Account')
    oneStepAheadReport('#runTripSearch', 'trip-search?v=Trip Search')
    
    function oneStepAheadReport(executor, url) {
        $(executor).click(function() {
            $identifier = $(this).attr('class')
            if($identifier === 'uni') {
                $client = $('#client').val()
                if($client == 0) {
                    validator('#validator', 'A client is required for an unpaid invoice')
                    return false
                }
                submit(url, $client)
            }
            if($identifier == 'pi') {
                $piDateFrom = $('#piDateFrom').val()
                $piDateTo = $('#piDateTo').val()
                $piClient = $('#paidInvoicesClient').val()
                if($piDateFrom == '' || $piDateTo == '') {
                    validator('#validator', 'Date range criteria for paid invoices is incomplete.')
                    return false
                }
                const payload = {
                    pi_date_from: $piDateFrom,
                    pi_date_to: $piDateTo,
                    pi_client_id: $piClient
                }
                submit(url, JSON.stringify(payload))
            }
            if($identifier == 'stat') {
                $tracker = $('#tracker').val()
                $uninvoicedClientId = $('#uninvoicedClientId').val()
                if($tracker == '') {
                    validator('#validator', 'Current trip status is required.')
                    return false
                }
                const payload = {
                    tracker: $tracker,
                    clientId: $uninvoicedClientId
                }
                submit(url, JSON.stringify(payload))
            }
            if($identifier == 'invd') {
                $clientInvoiced = $('#clientInvoiced').val()
                $invDateFrom = $('#invDateFrom').val()
                $invDateTo = $('#invDateTo').val()
                if($invDateTo == '' || $invDateTo == '') {
                    validator('#validator', 'Your date range criteria for invoiced trips is incomplete.')
                    return false
                }
                const invoicePayload = {
                    invoice_date_from: $invDateFrom,
                    invoice_date_to: $invDateTo,
                    client: $clientInvoiced
                }
                submit(url, JSON.stringify(invoicePayload))
            }
            if($identifier == 'transporterAcc') {
                $transporter = $('#transporter').val()
                if($transporter == "0") {
                    validator('#validator', 'Choose a transporter, it\'s required.')
                    return false
                }
                $transporterDateFrom = $('#transporterDateFrom').val()
                $transporterDateTo = $('#transporterDateTo').val()
                if(
                    ($transporterDateFrom !== '' && $transporterDateTo === '') || 
                    ($transporterDateTo !== '' && $transporterDateFrom === '')) {
                    validator('#validator', 'Check your transporter date range selection, something is not right about it.')
                    return false
                }
                const payload = {
                    transporter: $transporter,
                    transporter_date_from: $transporterDateFrom,
                    transporter_date_to: $transporterDateTo
                }
                submit(url, JSON.stringify(payload))
            }
            if($identifier === 'tripSearch') {
                $tripSearchFrom = $('#tripSearchFrom').val()
                $tripSearchTo = $('#tripSearchTo').val()
                $search = $('#search').val();
                const payload = {
                    from: $tripSearchFrom,
                    to: $tripSearchTo,
                    search: $search
                }
                if($tripSearchFrom !='' && $tripSearchTo != '' && $search != '') {
                    validator('#validator', 'You can only search by a Trip ID or a date range')
                    return false;
                }
                submit(url, JSON.stringify(payload))
            }
        })
    }
    
    function validator(errorPlace, message) {
        $(errorPlace).text(message).fadeIn(2000).delay(3000).fadeOut(2000).addClass('text-danger font-weight-semibold')
    }

    function submit(url, payload) {
        $('#reporting').html('<i class="spinner icon-spinner3"></i>Please wait...').addClass('font-weight-semibold')
        $.get(`/finance/${url}`, { payload: payload }, function(data) {
            $('#reporting').html(data)
        })
    }

    $(document).on('click', '#exportBtn', function(e)
    {
        var name = Math.random().toString().substring(7)
        $("#reporting").table2excel({
            filename:`Report-${name}.xls`
        });
    })
})