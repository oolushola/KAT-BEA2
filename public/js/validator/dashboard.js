$(function() {
    $("#updateProfilePhoto").click(function(e) {
        e.preventDefault();
        $file = $("#file").val();
        if($file == "") {
            errorMessage(
                '#loader',
                'Choose a picture to be uploaded',
                'error'
            )
            return false;
        }else{
            if($file !== '') {
                var ftype = $("#ftype").val();
                validateCsv(ftype);
                var filecheck = $("#filecheck").val();
                if(filecheck == "0"){return false;}
            }
        }
        successful(
            '#loader',
            `<i class="icon-spinner2 spinner mr-2"></i>Please Wait...`,
            'success'
        )
        $("#frmUploadProfilePhoto").submit();
    });



    function errorMessage(placeholder, message, className) {
        $(placeholder).html(`<i class='icon-x'></i>${message}`).addClass(className).fadeIn(3000).delay(3000).fadeOut(2000);
    }

    function successful(placeholder, message, className) {
        $(placeholder).html(`${message}`).addClass(className);
    }

    $("#changeAccountPassword").click(function(event) {
        event.preventDefault();
        $oldPassword = $("#oldPassword").val();
        if($oldPassword == '') {
            errorMessage('#loader2', 'Your old password is required.', 'error');
            $("#oldPassword").focus();
            return false;
        }
        $newPassword = $("#newPassword").val();
        if($newPassword == '') {
            errorMessage('#loader2', 'New password is required.', 'error');
            $("#newPassword").focus();
            return false;
        } else {
            if($newPassword.length < 6 ){
                errorMessage('#loader2', 'Minimum password length should be characters.', 'error');
                return false;
            }
        }
        $confirmNewPassword = $("#confirmNewPassword").val();
        if($confirmNewPassword == '') {
            errorMessage('#loader2', 'Please confirm your password by retyping', 'error');
            $("#confirmNewPassword").focus();
            return false;
        } else {
            if($newPassword !== $confirmNewPassword) {
                errorMessage('#loader2', 'Password does not match', 'error');
                return false;
            }
        }
        $.post('/change-password', $("#frmChangePassword").serialize(), function(data) {
            if(data == "changed"){
                successful('#loader2',`<i class="icon-spinner2 spinner mr-2"></i>Password changed successfully.`,'success'
                );
                $url = '';
               window.location = $url;
               return 'here';
            } else{
                if(data == 'wrongpass'){
                    errorMessage('#loader2', 'Sorry, the Old Password you entered is incorrect', 'error');
                    return false;
                } else {
                    errorMessage('#loader2', 'Something went wrong! Try Again.', 'error');
                    return false;
                }
            }
        });

    });

    $("#frmUploadProfilePhoto").ajaxForm(function(data) {
        if(data == "uploaded"){
            successful(
                '#loader',
                `<i class="icon-spinner2 spinner mr-2"></i>Photo updated successfully.`,
                'success'
            );
            $pageReloader = '';
            window.location = $pageReloader;
        } else {
            errorMessage(
                '#loader',
                'Something went wrong! Try Again.',
                'error'
            );
        }
    });

    $('#previousMonthTarget').change(function(){
        $selectedMonth = $('#previousMonthTarget').val();
        $.get('/monthly-target-graph', {selected_month:$selectedMonth}, function(response){
            $gateOutCount = response[0];
            $target = response[1];

            if($target.length <= 0){
                $targetForSelectedMonth = 150; 
            } else{ 
                $targetForSelectedMonth = response[1][0].target;
            }
            $percentageRate = $gateOutCount / $targetForSelectedMonth * 100;
            $percentageDisplay = `${$percentageRate.toFixed(2)}% of ${$targetForSelectedMonth}`
            $valueDisplay = `${$gateOutCount} of ${$targetForSelectedMonth}`

            $('#target-value').html($valueDisplay);
            $('#target-percentage__value').html($percentageDisplay);

            return targetPieChart($selectedMonth, $targetForSelectedMonth, $gateOutCount);
        });
        
    })

    $('#clientTripStatus').change(function(){
        $clientId = $('#clientTripStatus').val();
        $.get('/client-trip-status-chart', {client_id:$clientId}, function(response){            
            $labels = ['Gate In', 'At Loading Bay', 'On Journey', 'At Destination', 'Offloaded'];
            return masterBarChart('masterTripChart', $labels, response);
        })
    })



    $('#weekOne').blur(function(){
        $fromDateValue = $(this).val();
        $('#currentWeekInView').val($fromDateValue);
    });

    $('#weekTwo').blur(function(){
        $toDateValue = $(this).val();
        $('#presentDay').val($toDateValue);
    });

    $('#searchByWeek').click(function(){
        $currentWeekInView = $('#currentWeekInView').val();
        $presentDay = $('#presentDay').val();
        $.get('/gatedout-selected-week', {from:$currentWeekInView, to:$presentDay}, function(data){
            graphWeeklyDisplay($currentWeekInView, $presentDay, data[0].TotalWeekly);
        })

    });

    $('#compareTheTwoMonths').click(function(){
        $firstMonthComparator = $('#firstMonthComparator').val();
        $secondMonthComparator = $('#secondMonthComparator').val();
        $.get('/gatedout-months-comparison', {firstMonth:$firstMonthComparator, secondMonth:$secondMonthComparator}, function(response){
            twoMonthsComparator($firstMonthComparator, $secondMonthComparator, response);
        })
    })


    function graphWeeklyDisplay(from, to, exactData) {
        var ctx = document.getElementById('gatedOutForTheWeek');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Gated Out Trips for Week in view'],
                datasets: [{
                    label: `From [${from}] To [${to}]`,
                    data: [exactData],
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
                }
            }
        });
    }

    function twoMonthsComparator(firstMonth, secondMonth, exactData){
        var ctx = document.getElementById('gatedOutForTheMonth');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [firstMonth, secondMonth],
                datasets: [{
                    label: `${firstMonth} - ${secondMonth}`,
                    data: exactData,
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
                }
            }
        });   
    }


    // month data visualization
    $('#monthDv').click(function(){
        $checker = $(this).is(':checked');
        if($checker){
            $('#weekRangeDv').attr('disabled', 'disabled');
            $('#dayDv').attr('disabled', 'disabled');
            $('#monthPlaceHolder').removeClass('hidden')
        }
        else{
            $('#weekRangeDv').removeAttr('disabled');
            $('#dayDv').removeAttr('disabled');
            $('#monthPlaceHolder').addClass('hidden')
        }
    });

    // weekly data visualization
    $('#weekRangeDv').click(function(){
        $checker = $(this).is(':checked');
        if($checker){
            $('#monthDv').attr('disabled', 'disabled');
            $('#dayDv').attr('disabled', 'disabled');
            $('#weekPlaceHolder').removeClass('hidden')
        }
        else{
            $('#monthDv').removeAttr('disabled');
            $('#dayDv').removeAttr('disabled');
            $('#weekPlaceHolder').addClass('hidden')
        }
    });

    // Day data visualization
    $('#dayDv').click(function(){
        $checker = $(this).is(':checked');
        if($checker){
            $('#monthDv').attr('disabled', 'disabled');
            $('#weekRangeDv').attr('disabled', 'disabled');
            $('#dayPlaceHolder').removeClass('hidden')
        }
        else{
            $('#monthDv').removeAttr('disabled');
            $('#weekRangeDv').removeAttr('disabled');
            $('#dayPlaceHolder').addClass('hidden')
        }
    });


    $('#preferedMonth').change(function(){
        $selected_month = $('#preferedMonth').val();
        $.get('/loading-site-monthly', {selected_month:$selected_month}, function(response){
            $loadingSiteCounter = response[0];
            $exactLoadingSiteName = response[2];
            dataVisualizationOfLoadingSite($exactLoadingSiteName, $loadingSiteCounter, `${$selected_month} Loading Site Chart`);
        });
    });

    $('#searchLoadingSiteByWeek').click(function(){
        $loadingSiteWeekOne = $('#loadingSiteWeekOne').val();
        $loadingSiteWeekTwo = $('#loadingSiteWeekTwo').val();

        $.get('/loading-site-weekly', {weekOne:$loadingSiteWeekOne, weekTwo:$loadingSiteWeekTwo}, function(response){
            $loadingSiteCounter = response[0];
            $exactLoadingSiteName = response[2];
            dataVisualizationOfLoadingSite($exactLoadingSiteName, $loadingSiteCounter, `${$loadingSiteWeekOne} - ${$loadingSiteWeekTwo} Loading Site Chart`);
        });
    });

    $('#specificDay').change(function(){
        $choosenDay = $('#specificDay').val();
        $.get('/loading-site-specific-day', {choosen_day:$choosenDay}, function(response){
            $loadingSiteCounter = response[0];
            $exactLoadingSiteName = response[2];
            dataVisualizationOfLoadingSite($exactLoadingSiteName, $loadingSiteCounter, `${$choosenDay} Loading Site Chart`);
        });
    });
    


    function dataVisualizationOfLoadingSite(loadingSitesName, loadingCount, description){
        var masterCanvasBar = document.getElementById('masterBarChart');
                
        var myChart = new Chart(masterCanvasBar, {
            type: 'bar',
            data: {
                labels: loadingSitesName,
                datasets: [{
                    label: description,
                    data: loadingCount,
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
                }
            }
        });

    }

    function targetPieChart(selectedMonth, targetForTheMonth, gateOutForTheMonth){
        var ctx = document.getElementById('targetProcessChart');

        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [selectedMonth, 'Actualized'],
                datasets: [{
                    label: 'Monthly Target Statistics',
                    data: [targetForTheMonth, gateOutForTheMonth],
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

        });
    }

    function masterBarChart(canvasId, labels, exactData) {
        var ctx = document.getElementById(canvasId);
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: `Trip Status for a client`,
                    data: exactData,
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
                }
            }
        });
    }

    return masterBarChart(canvasId, labels, exactData);

});