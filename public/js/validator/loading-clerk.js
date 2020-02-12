$(function(){
    $(".fieldOps").click(function(){
        $value = $(this).prop("value");
        $value ? $("#fieldOpsType").val($value):$("#fieldOpsType").val("");
    })
    $("#addFieldOps").click(function(e) {
        e.preventDefault();
        validateFieldOps('/loading-clerk');
    });

    $("#updateFieldOps").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateFieldOps(`/loading-clerk/${$id}`);
    });

    function validateFieldOps(url) {
        $("#loader").removeClass('error').html('');
        $firstName = $("#firstName").val();
            if($firstName == '') {
                $("#loader").html('First name is required.').addClass('error');
                return;
            }
        $lastName = $("#lastName").val();
            if($lastName == '') {
                $("#loader").html('Last name is required.').addClass('error');
                return;
            }
        $email = $("#email").val();
            if($email == "") {
                $("#loader").html('Email is required.').addClass('error');
                return;
            }
            else {
                if(validateEmail($email)===false) {
                    $("#loader").html('Only valid email is required.').addClass('error');
                    return;
                }
            }
        $location = $("#location").val();
            if($location == '0') {
                $("#loader").html('Location of the field operation is required.').addClass('error');
                return;
            }
        $fieldOpsType = $("#fieldOpsType").val();
            if($fieldOpsType == '') {
                $("#loader").html('You have to choose either a loading or offloading clerk.').addClass('error');
                return;
            }
        $address = $("#address").val();
            if($address == '') {
                $("#loader").html('Address is required.').addClass('error');
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmFieldOps").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A field ops office with ${$email} already exists.`).addClass('error');
                return false;
            }
            else {
                if(data === 'saved' || 'updated') {
                    $("#loader").html(`Field ops record successfully ${data}.`).addClass('error').fadeIn(3000).fadeOut(5000);
                    setTimeout(function(){
                        $pageReload = '/loading-clerk';
                        window.location=$pageReload;
                    }, 2000);
                }
            }
        })
    }

    function validateEmail(email) {
	    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if( !emailReg.test( email ) ) {
            return false;
        }
        else {
            return true;
        }
    }
})