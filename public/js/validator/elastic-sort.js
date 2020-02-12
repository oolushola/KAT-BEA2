$(function() {
    $('.esearch').click(function() {
        $value = $(this).attr('value');
        if($value == 0){
            $('#generalSorter').removeClass('hidden');
            $('#yearSortHolder').addClass('hidden');
            $('#monthSortHolder').addClass('hidden');
            $('#weeklySortHolder').addClass('hidden');
        }
        if($value == 1) {
            $('#generalSorter').addClass('hidden');
            $('#yearSortHolder').removeClass('hidden');
            $('#monthSortHolder').addClass('hidden');
            $('#weeklySortHolder').addClass('hidden');
        }

        if($value == 2) {
            $('#generalSorter').addClass('hidden');
            $('#yearSortHolder').addClass('hidden');
            $('#monthSortHolder').removeClass('hidden');
            $('#weeklySortHolder').addClass('hidden');
        }

        if($value == 3) {
            $('#generalSorter').addClass('hidden');
            $('#yearSortHolder').addClass('hidden');
            $('#monthSortHolder').addClass('hidden');
            $('#weeklySortHolder').removeClass('hidden');
        }

    });



    $("#yearInview").change(function() {
        $choosenYear = $("#yearInview").val();
        loader('#contentPlaceholder');
        $.get('/filter-by-year', {choosen_year:$choosenYear}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });

    $('#yearAndMonth').change(function() {
        $choosenYear = $("#yearInview").val();
        $month = $("#yearAndMonth").val();
        loader('#contentPlaceholder');
        $.get('/filter-year-month', {choosen_year:$choosenYear, month:$month}, function(data) {
            $("#contentPlaceholder").html(data);
        });    
    });

    $('#yearClientId').change(function() {
        $choosenYear = $("#yearInview").val();
        $client_id = $("#yearClientId").val();
        loader('#contentPlaceholder');
        $.get('/filter-year-client', {choosen_year:$choosenYear, client_id:$client_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });

    $('#yearLoadingSiteId').change(function() {
        $choosenYear = $("#yearInview").val();
        $loading_site_id = $("#yearLoadingSiteId").val();
        loader('#contentPlaceholder');
        $.get('/filter-year-loading-site', {choosen_year:$choosenYear, loading_site_id:$loading_site_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });

    $('#YeartransporterId').change(function() {
        $choosenYear = $("#yearInview").val();
        $transporter_id = $("#YeartransporterId").val();
        loader('#contentPlaceholder');
        $.get('/filter-year-transporter', {choosen_year:$choosenYear, transporter_id:$transporter_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });
    
    $('#yearProductId').change(function() {
        $choosenYear = $("#yearInview").val();
        $product_id = $("#yearProductId").val();
        loader('#contentPlaceholder');
        $.get('/filter-year-product', {choosen_year:$choosenYear, product_id:$product_id}, function(data) {
            $("#contentPlaceholder").html(data);
        });
    });

    $('#yearDestination').change(function() {
        $choosenYear = $("#yearInview").val();
        $state_id = $("#yearDestination").val();
        loader('#contentPlaceholder'); loader('#exactLocationPlaceholder')
        $.get('/filter-year-state', {choosen_year:$choosenYear, state_id:$state_id}, function(data) {
            $array = data.split('`');
            $("#contentPlaceholder").html($array[0]);
            $("#exactLocationPlaceholder").html($array[1]);
        });
    });

    /************ FILTER BY WEEK ************/
    $("#filterWeekWise").click(function() {
        $weekstartfrom = $('#weekstartfrom').val();
        $weekendto = $('#weekendto').val();
        $weekLoadingSiteId = $('#weekLoadingSiteId').val();
        $weekProductId = $('#weekProductId').val();

        if($weekstartfrom == '') {
            $('#responsePlace').html('Choose a date range from').addClass('error').fadeIn(1000).delay(3000).fadeOut(2000);
            return false;
        }

        if($weekendto == '') {
            $('#responsePlace').html('Choose a date range to').addClass('error').fadeIn(1000).delay(3000).fadeOut(2000);
            return false;
        }

        loader('#contentPlaceholder'); loader('#exactLocationPlaceholder')

        if($weekstartfrom != '' && $weekendto != '' && $weekLoadingSiteId != '0' && $weekProductId != '0') {
            $.get('/filter-week-all-criteria', {from:$weekstartfrom, to:$weekendto, loading_site_id:$weekLoadingSiteId, product_id:$weekProductId}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }

        if($weekstartfrom != '' && $weekendto != '' && $weekLoadingSiteId != '0' && $weekProductId == '0') {
            $.get('/filter-week-by-loadingsite', {from:$weekstartfrom, to:$weekendto, loading_site_id:$weekLoadingSiteId}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }

        if($weekstartfrom != '' && $weekendto != '' && $weekLoadingSiteId == '0' && $weekProductId != '0') { 
            $.get('/filter-week-by-product', {from:$weekstartfrom, to:$weekendto, product_id:$weekProductId}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }

        if($weekstartfrom != '' && $weekendto != '' && $weekLoadingSiteId == '0' && $weekProductId == '0') {
            $.get('/filter-by-weekonly', {from:$weekstartfrom, to:$weekendto}, function(data) {
                $("#contentPlaceholder").html(data);
            });        
        }


    });

    $("#filterMaster").click(function() {
        $year = $('#yearInview').val();
        $month = $('#yearAndMonth').val();
        $client = $('#yearClientId').val();
        $loadingSite = $('#yearLoadingSiteId').val();
        $transporter = $('#YeartransporterId').val();
        $product = $('#yearProductId').val();
        $destination = $('#yearDestination').val();
        $exactlocation = $('#yearExactLocation').val();

        /********* MONTH URI FILTERS ************/
        if($year == 0 && $month != 0 && $client != 0){
            $.get('/filter-month-client', {month:$month, client_id:$client}, function(data) {
                $("#contentPlaceholder").html(data);
            })
        }
        if($year == 0 && $month != 0 && $loadingSite !=0){
            $.get('/filter-month-loading-site', {month:$month, loading_site_id:$loadingSite}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }
        if($year == 0 && $month != 0 && $transporter !=0){
            $.get('/filter-month-transporters', {month:$month, transporter_id:$transporter}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }
        if($year == 0 && $month != 0 && $destination !=0 && $exactlocation == 0){
            $.get('/filter-month-destination', {month:$month, state_id:$destination}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }
        if($year == 0 && $month != 0 && $client !=0 && $loadingSite != 0 ){
            $.get('/filter-month-client-ls', {month:$month, client_id:$client, loading_site_id:$loadingSite}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }
        if($year == 0 && $month != 0 && $product !=0 && $loadingSite == 0 ){
            $.get('/filter-month-products', {month:$month, product_id:$product}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }
        
        if($year == 0 && $month != 0 && $destination !=0 && $exactlocation != 0 ){
            $.get('/filter-month-destination-el', {month:$month, state_id:$destination, exact_location_id:$exactlocation}, function(data) {
                $("#contentPlaceholder").html(data);
            });
        }
    })


    function loader(placeholder) {
        $(placeholder).html('<span class="icon-spinner3 spinner"></span>').addClass('success')
    }

});