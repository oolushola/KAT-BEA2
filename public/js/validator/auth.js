
$(function() {
    $(".userRole").click(function() {
        $value = $(this).attr("value");
        $("#roleId").val($value);
    });


    $("#onBoardUsers").click(function(e) {
        e.preventDefault();
        validateUser();

    });

    $('#updateUsers').click(function(e) {
        e.preventDefault();
        validateUser();
    })

    function validateUser() {
        $firstName = $("#firstName").val();
        if($firstName == "") {
            errorResponse(
                '#firstName', '#firstNameError', '<i class="icon-x"></i> First name is required.', 'error'
            )
            return false;
        }
        $lastName = $("#lastName").val();
        if($lastName == "") {
            errorResponse(
                '#lastName', '#lastNameError', '<i class="icon-x"></i> Last name is required.', 'error'
            )
            return false;
        }
        $email = $("#email").val();
        if($email == "") {
            errorResponse(
                '#email', '#emailError', '<i class="icon-x"></i>Email is required.', 'error'
            )
            return false;
        }
        else{
            if(validateEmail($email) === false) {
                errorResponse(
                    '#email', '#emailError', '<i class="icon-x"></i>Only valid email is required.', 'error'
                )
                return false;
            }
            
        }
        $phoneNumber = $("#phoneNumber").val();
        if($phoneNumber == "") {
            errorResponse(
                '#phoneNumber', '#phoneNumberError', '<i class="icon-x"></i>Phone number is required.', 'error'
            )
            return false;
        }
        $roleId = $("#roleId").val();
        if($roleId == 0) {
            errorResponse(
                '', '#roleSpecificationError', '<i class="icon-x"></i>Role specification is required.', 'error' 
            )
            return false;
        }
        $("#frmUserRegistration").submit();

    }

    $("#frmUserRegistration").ajaxForm(function(data) {
        if(data == "exists") {
            errorResponse(
                '#phoneNumber', '#errorLoader', '<i class="icon-x"></i>This email has been registered.', 'error'
            )
            return false;
        }
        else {
            if(data == 'saved') {
                errorResponse(
                    '#phoneNumber', '#errorLoader', '<i class="icon-x"></i>User information added successfully.', 'error'
                )
                $url = '/user-registration';
                window.location.href = $url;
            }
            else{
                alert('Oops, Smething went wrong!');
            }
        }
    })

    function errorResponse(fieldName, placeholder, message, className) {
        $(fieldName).focus();
        $(placeholder).html(message).addClass(className).fadeIn(2000).delay(2000).fadeOut(1000);
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



    // Login
    // $("#login").click(function(e) {
    //     e.preventDefault();
    //    $.post('/check-login', $("#frmLogin").serializeArray(), function(data) {
           
    //    })
    // })
});