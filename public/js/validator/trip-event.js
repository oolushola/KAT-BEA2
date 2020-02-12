$(function() {

    function checker(varname, placeholder, on, off, onVal, offVal, trackerLabel, field1, field2) {
        if(varname) {
            $(placeholder).val(on);
            $(trackerLabel).val(onVal);
            $(field1).removeAttr("disabled", "disabled");
            $(field2).removeAttr("disabled", "disabled")
        }
        else{
            $(placeholder).val(off);
            $(trackerLabel).val(offVal);
            $(field1).attr("disabled", "disabled");
            $(field2).attr("disabled", "disabled")
        }
    }
    $("#onjourney").click(function() {
        $checked = $(this).is(":checked");
        checker($checked, "#onJourneyStatus", 1, 0, 6, '', '#tracker');
    });

    $("#arrivedDestination").click(function() {
        $checked = $(this).is(":checked");
        checker($checked, "#destinationArrival", 1, 0, 7, '', '#tracker', '#timeArrivedDestination');
    });

    $("#offloading").click(function() {
        $checked = $(this).is(":checked");
        checker($checked, "#offloadingStatus", 1, 0, 8, '', '#tracker', '#offloadStartTime', '#offloadEndTime');
    });

    $("#updateTripEvent").click(function(e) {
        e.preventDefault();
        if(validateTripJourney() == false) { return false;};
        $id = $("#id").val();
        submit(`/trip-event/${$id}`);
    })

    $("#addTripEvent").click(function(e) {
        e.preventDefault();
        if(validateTripJourney() == false) { return false;};
        submit('/trip-event');
    });

    function validateTripJourney() {
        $trackerStatus = $("#trackerStatus").val();
        if($trackerStatus < 5) {
            errorMessage("#loader", "Sorry, you can't add an event for a trip that is yet to be gated out.", "error");
            return false;
        }
        $tracker = $("#tracker").val();
        if($tracker == "") {
            errorMessage("#loader", "Please, choose the status of this trip", "error");
            return false;
        }
        $onJourneyStatus = $("#onJourneyStatus").val();
        if($onJourneyStatus == 1) {
            $locationCheckOne = $("#locationCheckOne").val();
            if($locationCheckOne == "") {
                errorMessage("#loader", "Please, at least a date and time  status report is expected per day", "error");
                return false;
            }
            $localtionOneComment = $("#localtionOneComment").val();
            if($localtionOneComment == "") {
                errorMessage("#loader", "Give a remark", "error");
                return false;
            }
        }
        $destinationArrival = $("#destinationArrival").val();
        if($destinationArrival == 1) {
            $timeArrivedDestination = $("#timeArrivedDestination").val();
            if($timeArrivedDestination == ""){
                errorMessage("#loader", "Please, choose the time it arrived destination.", "error");
                return false;
            }
        }
        $offloadingStatus = $("#offloadingStatus").val();
        if($offloadingStatus == 1) {
            $offloadStartTime = $("#offloadStartTime").val();
            if($offloadStartTime == ""){
                errorMessage("#loader", "Please, choose the time it started offloading", "error");
                return false;
            }

            $offloadEndTime = $("#offloadEndTime").val();
            if($offloadEndTime == ""){
                errorMessage("#loader", "Please, choose the time it ended offloading", "error");
                return false;
            }
        }
        return true;
    }

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
    

    function errorMessage(placeholder, message, className) {
        $(placeholder).html(message).addClass(className).fadeIn(3000).delay(6000).fadeOut(2000);
    }

})