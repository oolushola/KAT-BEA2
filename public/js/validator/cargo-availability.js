$(function(){
    $("#addTripRequest").click(function(e) {
        e.preventDefault();
        validateTripRequest('/cargo-availability');
    });

    $("#updateTripRequest").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateTripRequest(`/cargo-availability/${$id}`);
    })

    function validateTripRequest(url) {
        $("#loader").removeClass('error').html('');
        $clientId = $("#clientId").val();
            if($clientId == '0') {
                $("#loader").html('Choose a client.').addClass('error');
                return;
            }
        $availableOrder = $("#availableOrder").val();
            if($availableOrder == "") {
                $("#loader").html('Available order is required.').addClass('error');
                $("#availableOrder").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmCargoAvailability").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A truck with ${$truckNumber} already exists.`);
                return false;
            }
            else {
                if(data === 'saved' || data == 'updated') {
                    $("#loader").html(`Truck ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(5000);
                    $pageReload = '/cargo-availability';
                    window.location=$pageReload;
                }
            }
        })
    }

})