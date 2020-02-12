$(function() {
    $("#clientId").change(function() {
        $client_id = $("#clientId").val();
        loader('#loadingSitePlaceholder');
        loader('#contentPlaceholder');
        $.get('/client-trip-sort', {client_id : $client_id}, function(data) {
            $array = data.split('`');
            $('#loadingSitePlaceholder').html($array[0]);
            $("#contentPlaceholder").html($array[1]);

        });
        
    });


    $(document).on('change', '#loadingBayId', function() {
        $loading_site_id = $("#loadingBayId").val();
        $client_id = $("#clientId").val();
        loader('#contentPlaceholder');
        $.get('/sort-loading-site-client', {client_id:$client_id, loading_site_id:$loading_site_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });

    $("#status").change(function() {
        $status_id = $("#status").val();
        loader('#contentPlaceholder');
        $.get('/sort-tracker', {tracker:$status_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });

    $("#waybillstatus").change(function() {
        $waybillStatus = $("#waybillstatus").val();
        loader('#contentPlaceholder');
        $.get('/sort-waybill-status', {waybill_status:$waybillStatus}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    })

    $("#transporterId").change(function() {
        $transporter_id = $("#transporterId").val();
        loader('#contentPlaceholder');
        $.get('/sort-transporters', {transporter_id:$transporter_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    })


    function loader(placeholder) {
        $(placeholder).html('<span class="icon-spinner3 spinner"></span>').addClass('success')
    }


    $("#clientReportId").change(function() {
        $client_id = $("#clientReportId").val();
        loader('#contentPlaceholder');
        $.get('/client-report', {client_id:$client_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });

    $(document).on('click', '#downloadClientReport', function(){
        var name = Math.random().toString().substring(7);
        $("#exportTableData").table2excel({
            filename:`${name}-report.xls`
        });
    });







});