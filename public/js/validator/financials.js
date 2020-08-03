$(function(){
    $('#waybillStatus').change(function(){
        $waybillStatus = $('#waybillStatus').val();
        $uri = '';

        if($waybillStatus == 1){
            $uri = '/healthy-waybill';
        }

        if($waybillStatus == 2) {
            $uri = '/warning-waybill';
        }

        if($waybillStatus == 3) {
           $uri = '/extreme-waybill'; 
        }

        $.get($uri, {waybill_status:$waybillStatus}, function(response){
            $('#contentPlaceholder').html(response);
        })
    })


    $("#totalClientRate").html($('#clientRate').val());
    $("#totalTransporterRate").html($('#transporterRate').val());
    $("#totalGrossMargin").html($('#grossMargin').val());
    $("#averagePercentageMarkup").html('%'+$('#percentageMarkup').val());
    $("#averagePercentageMargin").html('%'+$('#percentageMargin').val());
    $("#totalAdvancePaid").html('&#x20a6;'+$('#advancePaid').val());
    $("#totalBalancePaid").html('&#x20a6;'+$('#balancePaid').val());
    $("#totalAmountPaid").html('&#x20a6;'+$('#totalAmount').val());

    $(document).on('dblclick', '.clientRate', function() {
        $tripId = $(this).attr("value")
        $('#defaultClientRate'+$tripId).addClass('hidden')
        $('#changeClientRate'+$tripId).removeClass('hidden')
    })


    $(document).on('keypress', '.updateClientRate', function($event) {
        if($event.keyCode === 13) {
            $tripId = $(this).attr('title')
            $oldClientRate = $(this).attr('value');
            $newClientRate = eval($(this).val());
            if($oldClientRate === $newClientRate) {
                $('#defaultClientRate'+$tripId).removeClass('hidden')
                $('#changeClientRate'+$tripId).addClass('hidden')
                return false
            }
            else{
                $('#clientRate'+$tripId+'Loader').html('<i class="spinner icon-spinner3"></i>')
                $.get('/update-client-rate/'+$tripId, {client_rate: $newClientRate }, function(data) {
                    if(data === 'updated') {
                        $('#defaultClientRate'+$tripId).removeClass('hidden')
                        $('#changeClientRate'+$tripId).addClass('hidden')
                        $('#clientRate'+$tripId+'Loader').html('')
                        $('#defaultClientRate'+$tripId).html($newClientRate)
                    }
                })
                
            }
            
        }
        else{
            if($event.keyCode === 27) {
                $('#defaultClientRate'+$tripId).removeClass('hidden')
                $('#changeClientRate'+$tripId).addClass('hidden')
                return false
            }
        }
    })


    // 
    $(document).on('dblclick', '.transporterRate', function() {
        $tripId = $(this).attr("value")
        $('#defaultTransporterRate'+$tripId).addClass('hidden')
        $('#changeTransporterRate'+$tripId).removeClass('hidden')
    })

    $(document).on('keypress', '.updateTransporterRate', function($event) {
        if($event.keyCode === 13) {
            $tripId = $(this).attr('title')
            $oldTransporterRate = $(this).attr('value');
            $newTransporterRate = eval($(this).val());
            if($oldTransporterRate === $newTransporterRate) {
                $('#defaultTransporterRate'+$tripId).removeClass('hidden')
                $('#changeTransporterRate'+$tripId).addClass('hidden')
                return false
            }
            else{
                $('#transporterRate'+$tripId+'Loader').html('<i class="spinner icon-spinner3"></i>')
                $.get('/update-transporter-rate/'+$tripId, {newTrValue: $newTransporterRate }, function(data) {
                    if(data === 'updated') {
                        $('#defaultTransporterRate'+$tripId).removeClass('hidden')
                        $('#changeTransporterRate'+$tripId).addClass('hidden')
                        $('#transporterRate'+$tripId+'Loader').html('')
                        $('#defaultTransporterRate'+$tripId).html($newTransporterRate)
                    }
                })
            }        
        }
    })


    $('#clients').change(function() {
        $id = $(this).val();
        $.get('/client-loading-site-finance', {client_id:$id}, function(data){
            $("#loadingSitePlaceHolder").html(data);
        });
    })

    //
    $('#filterFinance').click(function() {
        $client = $('#clients').val();
        $loadingSite = $('#loadingSite').val();
        $invoiceStatus = $('#invoiceStatus').val();
        $invoiceNo = $('#invoiceNumber').val();
        $destination = $('#destination').val();
        $dateFrom = $('#dateFrom').val();
        $dateTo = $('#dateTo').val();
        $paymentStatus = $('#paymentStatus').val();

        if($client && $loadingSite && $invoiceStatus) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/client-loading-site-invoice-status', { 
                client: $client, loading_site: $loadingSite,  invoice_status: $invoiceStatus}, function(data) {
                $('#contentPlaceholder').html(data);
            })  
        }

        if($client != '' && $invoiceStatus != '' && !$paymentStatus && !$dateFrom && !$dateTo) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Please wait, this might take some time. </span>')
            $.get('/client-invoice-status', { client: $client, invoice_status: $invoiceStatus}, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if($client && !$invoiceStatus && $loadingSite) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Please wait, this might take some time.</span>')
            $.get('/finance-client-loading-site', { client: $client, loading_site: $loadingSite}, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if($client && $destination) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/finance-client-destination', { client: $client, destination: $destination}, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if($invoiceNo) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/finance-invoice', { invoice_no: $invoiceNo }, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if(!$client && !$invoiceStatus && $dateFrom && $dateTo) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/finance-date-range', { date_from: $dateFrom, date_to: $dateTo }, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if($client && !$invoiceStatus && $dateFrom && $dateTo) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/finance-client-date-range', { client: $client, date_from: $dateFrom, date_to: $dateTo }, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if(($client && $invoiceStatus && $paymentStatus) || ($client && $paymentStatus)) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/finance-client-invoice-payment', { client: $client, invoice_status: $invoiceStatus, payment_status: $paymentStatus }, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if(!$client && !$invoiceStatus && $paymentStatus) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/finance-payment-status', { payment_status: $paymentStatus }, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }

        if($client && $invoiceStatus && $dateFrom && $dateTo) {
            $('#contentPlaceholder').html('<span class="mt-2 mb-2"><i class="icon-spinner spinner ml-2"></i>Wait...</span>')
            $.get('/finance-client-invoice-date-range', { client: $client, invoice_status: $invoiceStatus, date_from: $dateFrom, date_to: $dateTo }, function(data) {
                $('#contentPlaceholder').html(data);
            })
        }
    })

    $('#clearFields').click(function() {
        $client = $('#clients').val('');
        $loadingSite = $('#loadingSite').val('');
        $invoiceStatus = $('#invoiceStatus').val('');
        $invoiceNo = $('#invoiceNumber').val('');
        $destination = $('#destination').val('');
        $dateFrom = $('#dateFrom').val('');
        $dateTo = $('#dateTo').val('');
        $paymentStatus = $('#paymentStatus').val('');
    })

    $('#showFilter').click(function() {
        $(this).addClass('hidden')
        $('#closeFilter').removeClass('hidden')
        $('#filterFinanceCriteria').removeClass('hidden')
    })

    $('#closeFilter').click(function() {
        $(this).addClass('hidden')
        $('#showFilter').removeClass('hidden')
        $('#filterFinanceCriteria').addClass('hidden')
    })


    $(document).on('click', '#downloadFinancial', function(event){
        event.preventDefault();
        var name = Math.random().toString().substring(7);
        $("#exportTableData").table2excel({
            filename:`finance-report-${name}.xls`
        });
    });
})