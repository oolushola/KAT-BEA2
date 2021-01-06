$(function() {

    $selectedCriteria = localStorage.getItem('filtered')
    if($selectedCriteria) {
        $(`#uncompletedTrips tr`).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf($selectedCriteria) > -1)
        });
    }

    autosearch("change", "#filterSelection", "#uncompletedTrips")
    autosearch("keyup", "#searchUncompletedTrips", "#uncompletedTrips")

    function autosearch(event, searchBoxId, dataSetId) {
        $(searchBoxId).on(event, function() {
            var value = $(this).val().toLowerCase();
            if(event == "change") {
                localStorage.setItem('filtered', value)
            }
            $(`${dataSetId} tr`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    }

    $('.addEvent').click(function() {
        $tracker = $(this).attr('id')
        $loadingSite = $(this).attr('name')
        $kaid = $(this).attr('title')
        $tripId = $(this).attr('value')
        $product = $(this).attr('data-prod')
        $('.modal-title').html($kaid+' : '+$loadingSite).addClass('font-weight-semibold')
        $('#tripId').val($tripId);
        $('#orderId').val($kaid);
        $('#loadingSite').val($loadingSite);
        $('#productCarried').val($product)
        $('#eventLogListings').html('<i class="spinner icon-spinner3"></i>')
        $.get('/event-log/', {tripId: $tripId, kaid: $kaid, loadingSite: $loadingSite, tracker: $tracker, }, function(data) {
            $('#eventLogListings').html(data)
        })
    })

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


    $('#tracker').change(function() {
        if($(this).val() == 6){
            $("#timeArrivedDestination").attr('disabled', 'disabled')
            $('#gateInDestinationTimestamp').attr('disabled', 'disabled')
            $('#offloadStartTime').attr('disabled', 'disabled')
            $('#offloadEndTime').attr('disabled', 'disabled')
            $('#offloadedLocation').attr('disabled', 'disabled')
            $('#offloadIssueType').attr('disabled', 'disabled')

            $('#waybillAndEirPlaceholder').addClass('d-none')
        }
        if($(this).val() == 7) {
            $("#timeArrivedDestination").removeAttr('disabled', 'disabled')
            $('#gateInDestinationTimestamp').removeAttr('disabled', 'disabled')
            $('#offloadStartTime').attr('disabled', 'disabled')
            $('#offloadEndTime').attr('disabled', 'disabled')
            $('#offloadedLocation').attr('disabled', 'disabled')
            $('#offloadIssueType').attr('disabled', 'disabled')

            $('#waybillAndEirPlaceholder').addClass('d-none')
        }
        if($(this).val() == 8) {
            $("#timeArrivedDestination").removeAttr('disabled', 'disabled')
            $('#gateInDestinationTimestamp').removeAttr('disabled', 'disabled')
            $('#offloadStartTime').removeAttr('disabled', 'disabled')
            $('#offloadEndTime').removeAttr('disabled', 'disabled')
            $('#offloadedLocation').removeAttr('disabled', 'disabled')
            $('#offloadIssueType').removeAttr('disabled', 'disabled')
            $('#locationCheckOne :input').removeAttr("disabled");
            $('#locationCheckTwo :input').removeAttr("disabled");

            $('#waybillAndEirPlaceholder').removeClass('d-none')
        }
    })


    $('#addTripEvent').click(function($e) {
        $e.preventDefault();
        validator('/trip-event')
    })

   

    $(document).on('click', '.updateTripEvent', function() {
        $('#loader').html('Wait, fetching data')
        $id = $(this).attr('id')
        $orderId = $(this).attr('name')
        $clientName = $(this).attr('value')
        $('#id').val($id);
        $.get('/trip/'+$orderId+'/'+$clientName+'/'+$id+'/edit', function(data) {
            $('#morningVisibility').val(data.location_check_one);
            $('#morningComment').val(data.location_one_comment);
            $('#morningIssueType').val(data.morning_issue_type);
            $('#afternoonVisibility').val(data.location_check_two);
            $('#afternoonRemark').val(data.location_two_comment);
            $('#afternoonIssueType').val(data.afternoon_issue_type);
            $('#timeArrivedDestination').val(data.time_arrived_destination);
            $('#gateInDestinationTimestamp').val(data.gate_in_time_destination);
            $('#offloadStartTime').val(data.offload_start_time);
            $('#offloadEndTime').val(data.offload_end_time);
            $('#offloadedLocation').val(data.offloaded_location);
            $('#offloadIssueType').val(data.offload_issue_type);
            $('#loader').html('')

            $('#updateTripEvent').removeClass('d-none')
            $('#addTripEvent').addClass('d-none')

            $('#patchMethod').val('PATCH')

            $('#frmTripEvent').attr('action', `/trip-event/${$id}`)
        });

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
            errorMessage('#tracker', '#loaderEvent', 'Trip status is required', 'error')
            return false
        }
        else{
            if($tracker == 6) {
                $lga = $('#lga').val();
                if($lga == "") {
                    errorMessage('#lga', '#loaderEvent', 'Nearest local govt. of the truck is required.', 'error')
                    return false
                }
                $visibility = $('#visibility').val();
                if($visibility == "") {
                    errorMessage('', '#loaderEvent', 'Morning or Afternoon visibility check is required.', 'error')
                    return false
                }
                else{
                    if($visibility == 1) {
                        $morningVisibility = $('#morningVisibility').val();
                        if($morningVisibility == "") {
                            errorMessage('#morningVisibility', '#loaderEvent', 'Morning visibility date and time is required.', 'error')
                            return false;
                        }
                        $morningRemark = $('#morningComment').val();
                        if($morningRemark == "") {
                            errorMessage('#morningComment', '#loaderEvent', 'Morning remark is required.', 'error')
                            return false;
                        }
                    }
                    if($visibility == 2) {
                        $afternoonVisibility = $('#afternoonVisibility').val();
                        if($afternoonVisibility == "") {
                            errorMessage('#afternoonVisibility', '#loaderEvent', 'Afternoon visibility date and time is required.', 'error')
                            return false;
                        }
                        $afternoonRemark = $('#afternoonRemark').val();
                        if($afternoonRemark == "") {
                            errorMessage('#afternoonRemark', '#loaderEvent', 'Afternoon remark is required.', 'error')
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
                    errorMessage('#timeArrivedDestination', '#loaderEvent', 'Time arrived destination is required.', 'error')
                    return false;
                }
                submit(url)                
            }

            if($tracker == 8) {
                $gateInDestinationTimestamp = $('#gateInDestinationTimestamp').val();
                if($gateInDestinationTimestamp == "") {
                    errorMessage('#gateInDestinationTimestamp', '#loaderEvent', 'Gate in destination timestamp is required.', 'error')                    
                    return false;
                }
                $tadChecker = $('#tadChecker').val();
                if($tadChecker == "") {
                    $timeArrivedDestination = $('#timeArrivedDestination').val();
                    if($timeArrivedDestination == "") {
                        errorMessage('#timeArrivedDestination', '#loaderEvent', 'Time arrived destination is required.', 'error')                    
                        return false;
                    }
                }
                $offloadStartTime = $('#offloadStartTime').val();
                if($offloadStartTime == "") {
                    errorMessage('#offloadStartTime', '#loaderEvent', 'Offload start time is required.', 'error')                    
                    return false;
                }
                $offloadEndTime = $('#offloadEndTime').val();
                if($offloadEndTime == "") {
                    errorMessage('#offloadEndTime', '#loaderEvent', 'Offload end time is required.', 'error')                    
                    return false;
                }
                $offloadedLocation = $('#offloadedLocation').val();
                if($offloadedLocation == "") {
                    errorMessage('#offloadedLocation', '#loaderEvent', 'Offload location is required.', 'error')
                    return false;
                }
                $product = $('#productCarried').val()
                if($product !== 'Container') {
                    //request for at least one waybill or eir to be uploaded.
                    $validFields = $('input[type="file"]').map(function() {
                        if($(this).val() !== "") {
                            return $(this)
                        }
                    }).get()
                    if(!$validFields.length) {
                        alert('At least one Signed Waybill or an EIR is required to be uploaded before offloading.')
                        return false
                    }
                }
                $ask = confirm('Are you sure about the offload issue type status?');
                if($ask) {
                    $('#frmTripEvent').submit() 
                }
                else{
                    return false;
                }
            }
        }
    }

    function submit(url) {
        $('#loaderEvent').html('<i class="icon-spinner2 spinner mr-2"></i>Please wait...').addClass('error')
        $.post(url, $("#frmTripEvent").serializeArray(), function(data) {
            if(data === 'cant_add') {
                errorMessage('#loaderEvent', '<i class="icon-x mr-2"></i>Sorry, you can only add a trip event per day, click on the edit icon to modify.', 'error');
                return
            }
            else {
                $resultArray = data.split('`')
                $('#loaderEvent').html('<i class="icon-checkmark2></i> Event added successfully').addClass('text-success');
                $('#eventLogListings').html($resultArray[1])
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


    $('#addMoreWaybillAndEir').click(function() {
        $moreImages = '<span class="mr-3">';
        $moreImages += '<input type="file" name="received_waybill_and_eir[]" id="receivedWaybillAndEir" class="font-size-xs mb-1 d-inline">';
        $moreImages += '<i class="icon-minus-circle2 text-danger removeWaybillAndEir" id=""></i>';
        $moreImages += '</div>';
        $('#waybillAndEirHolder').append($moreImages)
    })

    $(document).on('click', '.removeWaybillAndEir', function() {
        $(this).parent('span').remove()
    })


    $("#frmTripEvent").ajaxForm(function(data){
        $dataResult = data.split('`')
        if(data == 'exists'){
            errorMessage(
                '#loaderEvent',
                'This waybill already exists.',
                'error'
            )
            return false;
        }
        else {
            if($dataResult[0] == 'saved' || $dataResult[0] == 'updated') {
                window.location = '';
            }
        }
    });

    
})