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

    /** Dashboard Misc */

    autosearch('#searchDataset', '#masterDataTable')
    autosearch('#searchGatedOut', '.monthlyGatedOutData')
    autosearch('#specificDateRangeSearch', '.monthlyGatedOutData')
    autosearch('#searchSpecificDateGateOut', '#searchSpecificDateGateOutData')


    function autosearch(searchBoxId, dataSetId) {
        $(searchBoxId).on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(`${dataSetId} tr`).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
        });
    }
    $("#extremeValuation").html('&#x20a6;'+$('#calculatedValuation').val())

    $('#exportWaybillStatus').click(function() {
        var name = Math.random().toString().substring(7);
        $("#exportTableData").table2excel({
            filename:`Waybill-status-${name}-report.xls`
        });
    })

    // autosearch('#searchWaybillReportData', '#currentGateOutDataForWaybillReport')
    $('#searchWaybillReportData').on("change", function() {
        var value = $(this).val().toLowerCase();
        if(value == 0){
            $('.serialNumber').removeClass('d-none')
        }
        else{
            $(`#currentGateOutDataForWaybillReport tr`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
            $('.serialNumber').addClass('d-none')
        }
    });

});