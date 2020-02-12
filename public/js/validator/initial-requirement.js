$(function() {
    $("#searchTransporters").keyup(function() {
        $("#transporterList").removeClass("hidden");
        var value = $(this).val().toLowerCase();
        $("#transporterBank tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > 1)
        });
    });

    $("#searchTransporters").focusout(function() {
        $("#searchTransporters").val('');
        $("#transporterList").addClass("hidden");
    });

    $("#searchTrucks").keyup(function() {
        $("#truckList").removeClass("hidden");
        var value = $(this).val().toLowerCase();
        $("#truckBank tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > 1)
        });
        
    });

    $("#searchTrucks").focusout(function() {
        $("#searchTrucks").val('');
        $("#truckList").addClass("hidden");
    });


    $("#searchDrivers").keyup(function() {
        $("#driversList").removeClass("hidden");
        var value = $(this).val().toLowerCase();
        $("#driverBank tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > 1)
        });
    });

    $("#searchDrivers").focusout(function() {
        $("#searchDrivers").val('');
        $("#driversList").addClass("hidden");
    });


    
    //validate function for Transporters!
    $("#addTransporter").click(function(e) {
        e.preventDefault(); 
        $("#loader").removeClass('error').html('');
        $transporterName = $("#transporterName").val();
        if($transporterName == '') {
            $("#loader").html('Transporter Name is required.').addClass('error');
            $("#transporterName").addClass('element-error').focus();
            return;
        }
        $email = $("#email").val();
        if(($email !=='') && (validateEmail($email)===false)) {
            $("#loader").html('Only valid email is required.').addClass('error');
            $("#email").focus();
            return;
        }
        $phoneNumber = $("#phoneNumber").val();
        if($phoneNumber == '') {
            $("#loader").html('Phone number is required.').addClass('error');
            $("#phoneNumber").addClass('element-error').focus();
            return;
        }
        $address = $("#address").val();
            if($address == '') {
                $("#loader").html('Address is required.').addClass('error');
                $("#address").addClass('element-error').focus();
                return;
            }
        $bankName = $("#bankName").val();
        if($bankName == '') {
            $("#loader").html('Which bank does this tranporter uses? Click on the Bank Details Tab.').addClass('error');
            $("#bankName").addClass('element-error').focus();
            return 
        }
        $accountName = $("#accountName").val();
        if($accountName == '') {
            $("#loader").html('Bank account name is required.').addClass('error');
            $("#accountName").addClass('element-error').focus();
            return 
        }

        $accountNumber = $("#accountNumber").val();
        if($accountNumber == '') {
            $("#loader").html('Account No. of this transporter is required.').addClass('error');
            $("#accountNumber").addClass('element-error').focus();
            return 
        }

        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        submitValidatedRequest('/transporters', '#frmTransporter', 'Record already exists', 'Record saved successfully ');
    });



    $("#addTruck").click(function(e) {
        e.preventDefault();
        $("#loader3").removeClass('error').html('');
        $transporterId = $("#transporterId").val();
            if($transporterId == '0') {
                $("#loader3").html('Transporter is required.').addClass('error');
                return;
            }
        $truckTypeId = $("#truckTypeId").val();
            if($truckTypeId == '0') {
                $("#loader3").html('Truck type is required.').addClass('error');
                return;
            }
        $truckNumber = $("#truckNumber").val();
            if($truckNumber == "") {
                $("#loader3").html('Truck number is required.').addClass('error');
                $("#truckNumber").focus();
                return;
            }
        $('#loader3').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        submitValidatedRequest('/trucks', '#frmTrucks', 'Record already exists', 'Record saved successfully');
        
    });




    $("#saveDriverDetails").click(function(e) {
        e.preventDefault();  
        $("#loader2").removeClass('error').html('');
        
        $driversFirstName = $("#driversFirstName").val();
            if($driversFirstName == "") {
                $("#loader2").html('Driver\'s first name is required.').addClass('error');
                return;
            }
        $driversLastName = $("#driversLastName").val();
            if($driversLastName == "") {
                $("#loader2").html('Driver\'s last name is required.').addClass('error');
                return;
            }
        $driversPhoneNumber = $("#driversPhoneNumber").val();
            if($driversPhoneNumber == "") {
                $("#loader2").html('Driver\'s phone number is required.').addClass('error');
                $("#driversPhoneNumber").focus();
                return;
            }
        $motorBoyFirstName = $("#motorBoyFirstName").val();
            if($motorBoyFirstName == "") {
                $("#loader2").html('Motor boy\'s first name is required.').addClass('error');
                $("#motorBoyFirstName").focus();
                return;
            }
        $motorBoyLastName = $("#motorBoyLastName").val();
            if($motorBoyLastName == "") {
                $("#loader2").html('Motor boy\'s last name is required.').addClass('error');
                $("#motorBoyLastName").focus();
                return;
            }
        $motorBoyPhoneNumber = $("#motorBoyPhoneNumber").val();
            if($motorBoyPhoneNumber == "") {
                $("#loader2").html('Motor boy phone number is required.').addClass('error');
                $("#motorBoyPhoneNumber").focus();
                return;
            }

        $('#loader2').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        submitValidatedRequest('/drivers', '#frmDriver', 'Record already exists.', 'Record saved successfully');
    });


    function validateEmail(email) {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if( !emailReg.test( email ) ) {
        return false;
      }
      else {
        return true;
      }
    }




    function submitValidatedRequest(url, frmName, errorMessage, successMessage ) {
        $.post(url, $(frmName).serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(errorMessage);
                return false;
            }
            else {
                if(data === 'saved') {
                    $("#loader").html(successMessage).addClass('error').fadeIn(3000).fadeOut(5000);
                    setTimeout(function(){
                        $pageReload = '';
                        window.location=$pageReload;
                    }, 2000);
                }
            }
        })
    }


})