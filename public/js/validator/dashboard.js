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
        $("#dateRangeLoader").html("<i class='spinner icon-spinner2'></i> Please wait...").addClass('mt-2 font-weight-semibold text-primary')
        $.get('/gatedout-selected-week', {from:$currentWeekInView, to:$presentDay}, function(data){
            $dataArrayRecord = data[0];
            $dataArrayCount = data[1];
            $("#specificDataRangeRecord").html($dataArrayRecord);
            graphWeeklyDisplay($currentWeekInView, $presentDay, data[1].TotalWeekly);
            $("#dateRangeLoader").html("");

        })

    });

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

});