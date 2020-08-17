$(function() {

    $('#locationCheckOne :input').attr("disabled", true);
    $('#locationCheckTwo :input').attr("disabled", true);

    $('.visibility').click(function() {
        $value = $(this).attr('value');
        if($value == 'morningVisibility') {
            $('#locationCheckOne :input').removeAttr("disabled");
            $('#locationCheckTwo :input').attr("disabled", true);
            $('#visibility').val(1)
        }
        if($value == 'afternoonVisibility') {
            $('#locationCheckOne :input').attr("disabled", "disabled");
            $('#locationCheckTwo :input').removeAttr("disabled");
            $('#visibility').val(2)
        }
    })
    

    function submit(url) {
        errorMessage('#loader', '<i class="icon-spinner2 spinner mr-2"></i>Please wait...', 'error')
        $.post(url, $("#frmTripEvent").serializeArray(), function(data) {
            if(data === 'cant_add') {
                errorMessage('#loader', '<i class="icon-x mr-2"></i>Sorry, you can only add a trip event per day, click on the edit icon to modify.', 'error');
                return
            }
            else {
                if((data === 'saved') || (data === 'updated')){
                    errorMessage('#loader',`Trip event successfully ${data}`, 'error');
                    window.location = '';
                }
            }
        })
    }
    

    function errorMessage(element, placeholder, message, className) {
        $(element).focus().css({ border:'1px solid red'});
        $(placeholder).html(message).addClass(className).fadeIn(3000).delay(6000).fadeOut(2000);
    }

    $('.specificTripEvent').click(function() {
        $trip_id = $(this).attr('id');
        $("#contentPlaceholder").html('<i class="spinner icon-spinner2" style="font-size:150px;"></i> Please wait...')
       
        $.get('/specific-trip-thread', { id: $trip_id }, function(data) {
            $('#contentPlaceholder').html(data);
        })
    })


    $('#tracker').change(function() {
        if($(this).val() == 6){
            $("#timeArrivedDestination").attr('disabled', 'disabled')
            $('#offloadStartTime').attr('disabled', 'disabled')
            $('#offloadEndTime').attr('disabled', 'disabled')
            $('#offloadedLocation').attr('disabled', 'disabled')
            $('#offloadIssueType').attr('disabled', 'disabled')
        }
        if($(this).val() == 7) {
            $("#timeArrivedDestination").removeAttr('disabled', 'disabled')
            $('#offloadStartTime').attr('disabled', 'disabled')
            $('#offloadEndTime').attr('disabled', 'disabled')
            $('#offloadedLocation').attr('disabled', 'disabled')
            $('#offloadIssueType').attr('disabled', 'disabled')
        }
        if($(this).val() == 8) {
            $("#timeArrivedDestination").removeAttr('disabled', 'disabled')
            $('#offloadStartTime').removeAttr('disabled', 'disabled')
            $('#offloadEndTime').removeAttr('disabled', 'disabled')
            $('#offloadedLocation').removeAttr('disabled', 'disabled')
            $('#offloadIssueType').removeAttr('disabled', 'disabled')
            $('#locationCheckOne :input').removeAttr("disabled");
            $('#locationCheckTwo :input').removeAttr("disabled");
        }
    })


    $('#addTripEvent').click(function($e) {
        $e.preventDefault();
        validator('/trip-event')
    })

    $('#updateTripEvent').click(function($e) {
        $e.preventDefault();
        $('#locationCheckOne :input').removeAttr("disabled");
        $('#locationCheckTwo :input').removeAttr("disabled");
        $id = $("#id").val()
        validator('/trip-event/'+$id)
    })

    $trackerStatus = $('#trackerStatus').val();
    if($trackerStatus < 5) {
        errorMessage('', '#loader', 'This truck is yet to gate out. Hence, visibility is prohibited', 'error')
        $('.btn').attr('disabled', 'disabled')
        return false
    }


    const validator = (url) => {
        $tracker = $('#tracker').val()
        if($tracker == "") {
            errorMessage('#tracker', '#loader', 'Trip status is required', 'error')
            return false
        }
        else{
            if($tracker == 6) {
                $lga = $('#lga').val();
                if($lga == "") {
                    errorMessage('#lga', '#loader', 'Nearest local govt. of the truck is required.', 'error')
                    return false
                }
                $visibility = $('#visibility').val();
                if($visibility == "") {
                    errorMessage('', '#loader', 'Morning or Afternoon visibility check is required.', 'error')
                    return false
                }
                else{
                    if($visibility == 1) {
                        $morningVisibility = $('#morningVisibility').val();
                        if($morningVisibility == "") {
                            errorMessage('#morningVisibility', '#loader', 'Morning visibility date and time is required.', 'error')
                            return false;
                        }
                        $morningRemark = $('#morningComment').val();
                        if($morningRemark == "") {
                            errorMessage('#morningComment', '#loader', 'Morning remark is required.', 'error')
                            return false;
                        }
                    }
                    if($visibility == 2) {
                        $afternoonVisibility = $('#afternoonVisibility').val();
                        if($afternoonVisibility == "") {
                            errorMessage('#afternoonVisibility', '#loader', 'Afternoon visibility date and time is required.', 'error')
                            return false;
                        }
                        $afternoonRemark = $('#afternoonRemark').val();
                        if($afternoonRemark == "") {
                            errorMessage('#afternoonRemark', '#loader', 'Afternoon remark is required.', 'error')
                            return false;
                        }
                    }
                }
                
                $ask = confirm("Are you sure about the on journey issue type status?")
                if($ask) {
                    submit(url)
                }
                else {
                    return false;
                }
            }

            if($tracker == 7) {
                $timeArrivedDestination = $('#timeArrivedDestination').val();
                if($timeArrivedDestination == "") {
                    errorMessage('#timeArrivedDestination', '#loader', 'Time arrived destination is required.', 'error')
                    return false;
                }
                submit(url)                
            }

            if($tracker == 8) {
                $timeArrivedDestination = $('#timeArrivedDestination').val();
                if($timeArrivedDestination == "") {
                    errorMessage('#timeArrivedDestination', '#loader', 'Time arrived destination is required.', 'error')                    
                    return false;
                }
                $offloadStartTime = $('#offloadStartTime').val();
                if($offloadStartTime == "") {
                    errorMessage('#offloadStartTime', '#loader', 'Offload start time is required.', 'error')                    
                    return false;
                }
                $offloadEndTime = $('#offloadEndTime').val();
                if($offloadEndTime == "") {
                    errorMessage('#offloadEndTime', '#loader', 'Offload end time is required.', 'error')                    
                    return false;
                }
                $offloadedLocation = $('#offloadedLocation').val();
                if($offloadedLocation == "") {
                    errorMessage('#offloadedLocation', '#loader', 'Offload location is required.', 'error')
                    return false;
                }
                $ask = confirm('Are you sure about the offload issue type status?');
                if($ask) {
                    submit(url)
                }
                else{
                    return false;
                }
            }
        }
    }
})