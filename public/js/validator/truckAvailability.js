$(function(){

    $("#clientId").change(function() {
        $client_id = $("#clientId").val();
        $.get('/client-loading-site', {client_id:$client_id}, function(data){
            $array = data.split('`');
            $("#loadingSiteContainer").html($array[0]);
            $('#productContainer').html($array[1]);
        });
    });

    $("#searchTruckNo").keyup(function() {
        $("#truckNoLists").removeClass("hidden");
        $('#truckType').val('');
        $('#tonnage').val('');
        var value = $(this).val().toLowerCase();
        $("#truckBank tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > 1)
        });
    });

    $(document).on('click', '.truckNo', function(){
        $truck_id = $(this).attr('id');
        $text = $(this).html();
        $('#searchTruckNo').val($text);
        $('#truckIdValue').val($truck_id);
        $("#truckNoLists").addClass("hidden");
        $('#truckNumberChecker').val(1);

        $.get('/truck-info', {truck_id:$truck_id}, function(data){
            $("#truckType").val(data[0].truck_type);
            $("#tonnage").val(data[0].tonnage);
            $('#truckTonnage').val(data[0].tonnage);
        });
    });

    $('#closeTruckBank').click(function(){
        $('#truckNoLists').addClass('hidden');
    })


    // Search Transporters Bank History (transporters)
    $("#searchTransporter").keyup(function() {
        $("#transporterList").removeClass("hidden");
        $('#transporterNumber').val('');
        var value = $(this).val().toLowerCase();
        $("#transporterBank tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > 1)
        });
    });

    $(document).on('click', '.transporters', function(){
        $transporter_id = $(this).attr('id');
        $text = $(this).html();
        $('#searchTransporter').val($text);
        $('#transporterIdValue').val($transporter_id);
        $("#transporterList").addClass("hidden");
        $('#transporterChecker').val(1);

        $.get('/transporter-phone', {transporter_id:$transporter_id}, function(data){
            $("#transporterNumber").val(data[0].phone_no);
        });
    });

    $('#closeTransporterBank').click(function(){
        $('#transporterList').addClass('hidden');
    })


    // Search drivers bank history
    $("#searchDriver").keyup(function() {
        $("#driversList").removeClass("hidden");
        $('#driverPhoneNumber').val();
        $('#motorBoyName').val('');
        $('#motorBoyNumber').val();
        var value = $(this).val().toLowerCase();
        $("#driversBank tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > 1)
        });
    });

    $(document).on('click', '.drivers', function(){
        $driver_id = $(this).attr('id');
        $text = $(this).html();
        $('#searchDriver').val($text);
        $('#driverIdValue').val($driver_id);
        $("#driversList").addClass("hidden");

        $.get('/driver-info', {driver_id:$driver_id}, function(data){
            $("#driverPhoneNumber").val(data[0].driver_phone_number);
            $("#motorBoyName").val(data[0].motor_boy_first_name+' '+data[0].motor_boy_last_name);
            $("#motorBoyNumber").val(data[0].motor_boy_phone_no);
            $('#driverChecker').val(1);
        });
    });

    $('#closeDriverBank').click(function(){
        $('#driversList').addClass('hidden');
    })


    $("#destinationState").change(function() {
        $state_id = $("#destinationState").val();
        $('#exactLocationHolder').html('<i class="icon-spinner2 spinner mr-2"></i>Loading...');
        $.get('/exact-destination', {state_id:$state_id}, function(data){
            $("#exactLocationHolder").html(data);
        });
    });




    // click function to submit truck availability 
    $('#addTruckAvailability').click(function(e){
        e.preventDefault();
        if(validateTruckAvailability()==false){ return };
        submitTruckAvailability('/truck-availability', '#frmTruckAvailability', 'Do you wish to add more truck?', '/truck-availability', '/truck-availability-list');

    });


    const messageDisplayer = (placeHolder, message, className) => {
        $(placeHolder).html(message).addClass(className).fadeIn(1000).delay(2000).fadeOut(3000);
    }

    const validateTruckAvailability = () =>{
        let clientId = $('#clientId').val();
        if(clientId == ''){
            messageDisplayer('#errorMessage', 'Client is required.', 'error')
            return false;
        }
        let loadingSite = $('#loadingSite').val();
        if(loadingSite == ''){
            messageDisplayer('#errorMessage', 'Loading site is required.', 'error')
            return false
        }
        let truckNumber = $('#searchTruckNo').val();
        if(truckNumber == ''){
            messageDisplayer('#errorMessage', 'Truck number is required.', 'error')
            return false
        }
        let truckType = $('#truckType').val();
        if(truckType == ''){
            messageDisplayer('#errorMessage', 'Truck type is required.', 'error')
            return false
        }
        let tonnage = $('#tonnage').val();
        if(tonnage == ''){
            messageDisplayer('#errorMessage', 'Tonnage is required.', 'error')
            return false
        }
        let transporter = $('#searchTransporter').val();
        if(transporter == '') {
            messageDisplayer('#errorMessage', 'Transporter is required.', 'error')
            return false
        }
        let driver = $('#searchDriver').val();
        if(driver == ''){
            messageDisplayer('#errorMessage', 'Driver is required.', 'error')
            return false
        }
        let driverPhoneNumber = $('#driverPhoneNumber').val();
        if(driverPhoneNumber == ''){
            messageDisplayer('#errorMessage', 'Driver phone number is required.', 'error')
            return false
        }
        
        let productId = $('#productId').val();
        if(productId == ''){
            messageDisplayer('#errorMessage', 'Product is required.', 'error')
            return false
        }
        let destinationState = $('#destinationState').val();
        if(destinationState == ''){
            messageDisplayer('#errorMessage', 'Destination state is required.', 'error')
            return false
        }
        let exactLocation = $('#exactLocation').val();
        if(exactLocation == ''){
            messageDisplayer('#errorMessage', 'Exact location is required.', 'error')
            return false
        }
        let truckStatus = $('#truckStatus').val();
        if(truckStatus == ''){
            messageDisplayer('#errorMessage', 'Truck status is required.', 'error')
            return false
        }
    }

    const submitTruckAvailability = (serverPage, frmName, confirmationMessage, urlSamePage, urlRedirect) => {
        $.post(serverPage, $(frmName).serializeArray(), (data)=>{
            if(data == 'transporterNumberExists')
            {
                messageDisplayer('#errorMessage', '<i class="icon icon-x"></i>A transporter with this number already exists.', 'error');
                return false;
            }
            else if(data == 'invalidTruckType'){
                messageDisplayer('#errorMessage', 'The truck type format is invalid. Below are the list of acceptable truck types "Flat Bed", "Full Sided F/S", "Covered"', 'error');
            }
            else if(data == 'exists') {
                messageDisplayer('#errorMessage', 'This truck details already available in truck availability.', 'error');
            }
            else{
                if(data == 'saved'){
                    messageDisplayer('#errorMessage', '<i class="icon icon-checkmark2"></i>Truck successfully added to availability.', 'success');
                    const ask = confirm(confirmationMessage);
                    if(ask){
                        window.location.href = urlSamePage;
                    }
                    else{
                        window.location.href = urlRedirect;
                    }
                }
            }
        });
    }

    $('#storeMovedInTruckDetails').click(function(e){
        e.preventDefault();
        if(validateTruckAvailability()==false){ return };
        submitTruckAvailability('/add-movedintruck-availability', '#frmStoreTripFromTruckAvailability', 'Want to GATE IN more trucks?', '/truck-availability-list', '/truck-availability-list');

    });


    $('.updateAvailabilityStatus').click(function(e) {
        e.preventDefault();
        $currentStatus = $(this).attr('name');
        $truckNumber = $(this).attr('title');
        $('#truckStatus').val($currentStatus);
        $id = $(this).attr('id');
        $("#truckAvailabilityId").val($id);
        $('#textDescriptor').html(`TRUCK AVAILABILITY - UPDATE <strong>${$truckNumber}</strong> STATUS`);
    });

    $('#updateAvailabilityStatus').click(function(e){
        e.preventDefault();
        let truckStatus = $('#truckStatus').val();
        if(truckStatus == ''){
            messageDisplayer('#loader2', 'Truck status is required.', 'error')
            return false
        }
        $truckAvailabilityId = $('#truckAvailabilityId').val();
        $.post(`/truck-availability/${$truckAvailabilityId}`, $('#frmTruckAvailabilityUpdate').serialize(), function(data) {
            if(data == 'updated'){
                messageDisplayer('#loader2', 'Truck Status Updated Successfully', 'success')        
                $url = '/truck-availability-list';
                window.location = $url;        
            }
        });
    });

    $('.removeTruckAvailability').click(function(){
        $truckAvailabilityId = $(this).attr('id');
        $.post(`/truck-availability/${$truckAvailabilityId}`, $('#frmDeleteTruckAvailability').serialize(), function(data){
             if(data == 'deleted'){
                 alert('Truck successfully removed from availability.');
                 window.location = '/truck-availability-list';
             }
        })
    })

   


});