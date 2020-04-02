$(function() {
    $('.waybillChecker').click(function() {
        $conditionChecked = $(this).attr("title");
        if($conditionChecked == 'collected'){
            $('#proofOfWaybillUpload').removeClass('hidden');
            $('#waybillNotCollected').addClass('hidden');
            $('#waybillStatus').val(1);
        }
        else{
            if($conditionChecked == 'notCollected'){
                $('#proofOfWaybillUpload').addClass('hidden');
                $('#waybillNotCollected').removeClass('hidden');
                $('#waybillStatus').val(0);
            }
        }
    })

    $('.remarkOnTrips').click(function() {
        $title = $(this).attr('title');
        $id = $(this).attr('id');
        $('#titlePlace').html(`OFFLOADING REMARKS FOR : ${$title}`);
        $('#tripId').val($id);
    });

    $('#updateTripEvent').click(function($e) {
        $e.preventDefault();
        $timeArrivedDestination = $('#timeArrivedDestination').val();
        if($timeArrivedDestination == ''){
            $('#messageHolder').html('<i class="icon-x"></i>Date and Time arrived destination is required.').addClass('error')
            $('#timeArrivedDestination').focus();
            return false;
        }
        
        $timeOffloadStarted = $('#timeOffloadStarted').val();
        if($timeOffloadStarted == ''){
            $('#messageHolder').html('<i class="icon-x"></i>Date and Time offload begins is required.').addClass('error')
            $('#timeOffloadStarted').focus();
            return false;
        }

        $timeOffloadingEnded = $('#timeOffloadingEnded').val();
        if($timeOffloadingEnded == ''){
            $('#messageHolder').html('<i class="icon-x"></i>Date and Time offload finishes is required.').addClass('error')
            $('#timeOffloadingEnded').focus();
            return false;
        }
        $offloadedLocation = $('#offloadedLocation').val();
        if($offloadedLocation == ''){
            $('#messageHolder').html('<i class="icon-x"></i>The exact place where this truck offloaded is required.').addClass('error')
            $('#offloadedLocation').focus();
            return false;
        }
        $waybillChecker = $('.waybillChecker').is(':checked');
        if(!$waybillChecker){
            $("#messageHolder").html('<i class="icon-x"></i>We need to know if you were able to collect the waybill or').addClass('error'); 
            return false;
        }
        else{
            $waybillStatus = $('#waybillStatus').val();
            if($waybillStatus == 1){
                $receivedWaybill = $('#receivedWaybill').val();
                if($receivedWaybill == "") {
                    $("#messageHolder").html('<i class="icon-x"></i>Choose a file to be uploaded, should be a JPEG, PNG, GIF format').addClass('error');
                    $('#file').focus();
                    return false;
                }
            } else {
                if($waybillStatus == 0){
                    $waybilleNotReceived = $('#waybilleNotReceived').val();
                    if($waybilleNotReceived == "") {
                        $("#messageHolder").html('<i class="icon-x"></i>Please state the reason why you couldn\'t collect the waybill ').addClass('error');
                        $('#waybilleNotReceived').focus();
                    }
                }
            }
        }
        $('#messageHolder').html('<i class="spinner icon-spinner2"></i>Please wait...').addClass('text-warning');
        $('#frmUpdateTripEvent').submit()
    });


    $('.nofifyAdHoc').click(function($e) {
        $e.preventDefault();
        $event = $(this);
        $trip_id = $(this).attr("id");
        $tripDestination = $(this).attr("title");
        $userIdentifier = $('#userIdentifier').val();
        $(this).attr("disabled", "disabled").html('<i class="spinner icon-spinner2"></i>Notifying...');
        $.get('/offloading-clerk-notification', {trip_id:$trip_id, exact_location: $tripDestination, user_id: $userIdentifier}, function(data) {
            if(data == 'sent'){
                $event.html('<i class="icon-checkmark2"></i> Notification Sent.').addClass('bg-primary');
            } else {
                 if(data == "no_user_found"){
                     $event.html('<i class="icon-stack-cancel"></i> No User Found.').addClass('bg-danger');
                    //  icon-stack-cancel, icon-cancel-square;

                     return false;
                 } 
            }
        });
    })

    //Search Trips!
    $("#searchTrip").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable section").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });

    $('#frmUpdateTripEvent').ajaxForm(function(data) {
        if(data == 'error'){
            $("#messageHolder").html('<i class="icon-x"></i>Oops! something went wrong, please consult kaya tech support. Thank you').addClass('error').fadeIn(1000).delay(3000).fadeOut(2000);
        }
        else{
            if(data == "updated"){
                $("#messageHolder").html('<i class="icon-checkmark3"></i>Thank you, your remark will be picked up by the visibility team').addClass('success').fadeIn(1000).delay(3000).fadeOut(2000);
                setTimeout(() => {
                    $url = '/offloading/my-trips-view'
                    window.location = $url;
                }, 3000);
            }
        }
    });

    

    
})