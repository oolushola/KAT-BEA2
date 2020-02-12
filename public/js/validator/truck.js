$(function(){
    $("#addTruck").click(function(e) {
        e.preventDefault();
        validateTruck('/trucks');
    });

    $("#updateTruck").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateTruck(`/trucks/${$id}`);
    })

    function validateTruck(url) {
        $("#loader").removeClass('error').html('');
        $transporterId = $("#transporterId").val();
            if($transporterId == '0') {
                $("#loader").html('Transporter is required.').addClass('error');
                return;
            }
        $truckTypeId = $("#truckTypeId").val();
            if($truckTypeId == '0') {
                $("#loader").html('Truck type is required.').addClass('error');
                return;
            }
        $truckNumber = $("#truckNumber").val();
            if($truckNumber == "") {
                $("#loader").html('Truck number is required.').addClass('error');
                $("#truckNumber").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmTrucks").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A truck with ${$truckNumber} already exists.`);
                return false;
            }
            else {
                if(data === 'saved' || 'updated') {
                    $("#loader").html(`Truck ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(5000);
                    $pageReload = '/trucks';
                    window.location=$pageReload;
                    
                }
            }
        })
    }

})