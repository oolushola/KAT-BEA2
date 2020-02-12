$(function(){
    
    $("#addTruckType").click(function(e) {
        e.preventDefault();
        validateTruckType('/truck-types');
    });

    $("#updateTruckType").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateTruckType(`/truck-types/${$id}`);
    })

    function validateTruckType(url) {
        $("#loader").removeClass('error').html('');
        $truckTypeCode = $("#truckTypeCode").val();
            if($truckTypeCode == '') {
                $("#loader").html('Truck type code is required.').addClass('error');
                $("#truckTypeCode").addClass('element-error').focus();
                return;
            }
        $truckType = $("#truckType").val();
            if($truckType == "") {
                $("#loader").html('Truck type is required.').addClass('error');
                $("#truckType").focus();
                return;
            }
        $tonnage = $("#tonnage").val();
            if($tonnage == "") {
                $("#loader").html('Tonnage of this truck type is required.').addClass('error');
                $("#tonnage").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmTruckType").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A truck type code "${$truckTypeCode}" already exists.`).addClass('error');
                return false;
            }
            else {
                if(data === 'saved' || 'updated') {
                    $("#loader").html(`Truck type ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                    setTimeout(function(){
                        $pageReload = '/truck-types';
                        window.location=$pageReload;
                    }, 2000);
                }
                else{
                    return false;
                }
            }
        })
    }
})