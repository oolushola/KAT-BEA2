$(function() {

    $(document).on('click', '#downloadClientReport', function(event){
        event.preventDefault();
        var name = Math.random().toString().substring(7);
        $("#exportTableData").table2excel({
            filename:`${name}-report.xls`
        });
    });

    $(document).on('click', '#sendForComplete', function(e) {
        $('#contentLoader').html('<i class="icon icon-spinner2"></i> Please wait...');
        $.post('/completed-report', $('#frmCompleteClientReport').serializeArray(), function(data) {
            if(data == 'saved'){
                $('#contentLoader').html('Report completed successfully.').addClass('success');
                window.location = '';
            }
            else{
                return false;
            }
        })
    })

    //FILTER THE DATABASE TO GET THE BEST OF EVERY RESULT

    $('#shootByClient').click(function() {
        $clientTripDateFrom = $('#clientTripDateFrom').val();
        $clientTripDateTo = $('#clientTripDateTo').val();
        $clients = $('#clients').val();
        $clientLoadingSite = $('#clientLoadingSite').val();
        $clientTripStatus = $('#clientTripStatus').val();

        const payload = {
            client_date_from: $clientTripDateFrom,
            client_date_to: $clientTripDateTo,
            client_id: $clients,
            loading_site_id: $clientLoadingSite,
            trip_status: $clientTripStatus,
        }
        
        if($clientTripDateFrom !== '' && $clientTripDateTo !== '' && $clients !== '' && $clientLoadingSite !== '' && $clientTripStatus !== '') {
            submit('client/all', JSON.stringify(payload));
        }

        if($clientTripDateFrom !== '' && $clientTripDateTo !== '' && $clients !== '' && $clientLoadingSite !== '' && $clientTripStatus == '') {
            submit('client/date-range-client-and-status', JSON.stringify(payload));
        }

        if($clientTripDateFrom !== '' && $clientTripDateTo !== '' && $clients !== '' && $clientLoadingSite == '' && $clientTripStatus == '') {
            submit('client/date-range-and-client', JSON.stringify(payload));
        }

        if($clientTripDateFrom == '' && $clientTripDateTo == '' && $clients !== '' && $clientLoadingSite !== '' && $clientTripStatus != '') {
            submit('client/client-loading-site-and-trip-status', JSON.stringify(payload));
        }

        if($clientTripDateFrom == '' && $clientTripDateTo == '' && $clients !== '' && $clientLoadingSite == '' && $clientTripStatus !== '') {
            submit('client/client-and-status', JSON.stringify(payload));
        }

        if($clientTripDateFrom !== '' && $clientTripDateTo !== '' && $clients !== '' && $clientLoadingSite == '' && $clientTripStatus !== '') {
            submit('client/dateRangeClientStatus', JSON.stringify(payload));
        }
    })



    $('#shootByTransporter').click(function() {
        $transporter = $('#transporter').val()
        $transporterDateFrom = $('#transporterDateFrom').val()
        $transporterDateTo = $('#transporterDateTo').val()
        $transporterTripStatus = $('#transporterTripStatus').val()
        const payload = {
            transporter_id: $transporter,
            transporter_date_from: $transporterDateFrom,
            transporter_date_to: $transporterDateTo,
            transporter_trip_status: $transporterTripStatus,
            trip_status: $transporterTripStatus,
        }
        if($transporter !== '' && $transporterDateFrom == '' && $transporterDateTo == '' && $transporterTripStatus == '') {
            submit('transporters/transporter', JSON.stringify(payload))
        }
        if($transporter !== '' && $transporterDateFrom != '' && $transporterDateTo != '' && $transporterTripStatus == '') {
            submit('transporters/transporterAndDateRange', JSON.stringify(payload))
        }
        if($transporter !== '' && $transporterDateFrom != '' && $transporterDateTo != '' && $transporterTripStatus != '') {
            submit('transporters/all', JSON.stringify(payload))
        }
        if($transporter !== '' && $transporterDateFrom == '' && $transporterDateTo == '' && $transporterTripStatus != '') {
            submit('transporters/transporterAndTripStatus', JSON.stringify(payload))
        }
    })

    $('#shootByVoidedTrips').click(function($e) {
        $e.preventDefault;
        const payload = '';
        submit('trips/voided', payload);
    })

    $('#shootByTripStatusOnly').click(function() {
        $trackerStatus = $('#trackerStatus').val();
        const payload = {
            tracker: $trackerStatus
        }
        submit('trips/trip-status', JSON.stringify(payload))
    })


    $('.filter').click(function() {
        $('.filter').removeClass('bg-danger')
        $(this).addClass('bg-danger')

        $showDataIdentity = $(this).attr('data-id');
        $('.display').addClass('hidden')
        $(`#${$showDataIdentity}`).removeClass('hidden')
    })

    $('#clients').change(function() {
        $.get('/orders-loading-sites', { client_id: $(this).val() }, function(data) {
            $('#loadingsitePlace').html(data)
        })
    })

    function submit  (url, payload)  {
        $('#contentPlaceholder').html('<i class="icon-spinner3 spinner "></i> Wait...')
        $.get(`/trips/${url}`, { payload: payload }, function(data) {
            $('#contentPlaceholder').html(data)
        })
    }







});