$(function(){
    $("#saveDriverDetails").click(function(e) {
        e.preventDefault();
        validateDriver('/drivers');
    });

    $("#updateDriverDetails").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateDriver(`/drivers/${$id}`);
    })

    function validateDriver(url) {
        $("#loader").removeClass('error').html('');
        $driversFirstName = $("#driversFirstName").val();
            if($driversFirstName == "") {
                $("#loader").html('Driver\'s first name is required.').addClass('error');
                return;
            }
        $driversLastName = $("#driversLastName").val();
            if($driversLastName == "") {
                $("#loader").html('Driver\'s last name is required.').addClass('error');
                return;
            }
        $driversPhoneNumber = $("#driversPhoneNumber").val();
            if($driversPhoneNumber == "") {
                $("#loader").html('Driver\'s phone number is required.').addClass('error');
                $("#driversPhoneNumber").focus();
                return;
            }
        $motorBoyFirstName = $("#motorBoyFirstName").val();
            if($motorBoyFirstName == "") {
                $("#loader").html('Motor boy\'s first name is required.').addClass('error');
                $("#motorBoyFirstName").focus();
                return;
            }
        $motorBoyLastName = $("#motorBoyLastName").val();
            if($motorBoyLastName == "") {
                $("#loader").html('Motor boy\'s last name is required.').addClass('error');
                $("#motorBoyLastName").focus();
                return;
            }
        $motorBoyPhoneNumber = $("#motorBoyPhoneNumber").val();
            if($motorBoyPhoneNumber == "") {
                $("#loader").html('Motor boy phone number is required.').addClass('error');
                $("#motorBoyPhoneNumber").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmDrivers").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A driver with this ${$driversPhoneNumber} already exists.`);
                return false;
            }
            else {
                if(data === 'saved' || 'updated') {
                    $("#loader").html(`Driver's details ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(5000);
                    setTimeout(function(){
                        $pageReload = '/drivers';
                        window.location=$pageReload;
                    }, 3000);
                }
            }
        })
    }

    $("#bulkUpload").click(function() {
        $(this).css({display:'none'});
        $("#singleUpload").css({display:'inline-block'});
        $("#bulkUploadForm").css({display:'block'})
        $("#singleEntryForm").css({display:'none'});
    });

    $("#singleUpload").click(function() {
        $(this).css({display:'none'});
        $("#bulkUpload").css({display:'inline-block'});
        $("#bulkUploadForm").css({display:'none'})
        $("#singleEntryForm").css({display:'block'});
    });

    $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

})