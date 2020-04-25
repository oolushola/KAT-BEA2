$(function() {
       
    $("#clientId").change(function() {
        $client_id = $("#clientId").val();
        $.get('/client-loading-site', {client_id:$client_id}, function(data){
            $array = data.split('`');
            $("#loadingSiteContainer").html($array[0]);
            $('#productContainer').html($array[1]);
        });
    });

    /**
     * @description GET METHOD for transporter to get phone number
     * @return {Object}
     */

    $("#transporter").change(function() {
        $transporter_id = $("#transporter").val();
        $.get('/transporter-phone', {transporter_id:$transporter_id}, function(data){
            $("#transporterNumber").val(data[0].phone_no);
        });
    });

    /**
     * @description GET METHOD to get truck type and tonnage
     * @return [{Object}] Array of Object
     */

     $("#truckId").change(function() {
        $truck_id = $("#truckId").val();
        $.get('/truck-info', {truck_id:$truck_id}, function(data){
            $("#truckType").val(data[0].truck_type);
            $("#tonnage").val(data[0].tonnage);
            $('#truckTonnage').val(data[0].tonnage);
        });
     });

     /**
      * @description GET METHOD to get driver's information
      * @return [{Object}]
      */

    $("#driverId").change(function() {
        $driver_id = $("#driverId").val();
        $.get('/driver-info', {driver_id:$driver_id}, function(data){
            $("#driverPhoneNumber").val(data[0].driver_phone_number);
            $("#motorBoyName").val(data[0].motor_boy_first_name+' '+data[0].motor_boy_last_name);
            $("#motorBoyNumber").val(data[0].motor_boy_phone_no);
        });
    });

    /**
     * @description GET METHOD to get driver's information
     * @return [{Object}]
     */

    $("#destinationState").change(function() {
        $state_id = $("#destinationState").val();
        $('#exactLocationHolder').html('<i class="icon-spinner2 spinner mr-2"></i>Loading...');
        $.get('/exact-destination', {state_id:$state_id}, function(data){
            $("#exactLocationHolder").html(data);
        });
    });

    
    function submitTrip(uri, frmName, pageReloadUrl) {
        $ask = confirm('Are you sure all informations supplied are accurate? once submitted, it can\t be modified. Click on cancel to proofread');
        if($ask){
            $("#loader").html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
            $.post(uri, $(frmName).serialize(), function(response) {
                if(response === 'exists') {
                    $("#loader").html('Operation Aborted. The last trip made by this truck has not been marked offloaded. Please contact the visibility team').addClass("error", "font-weight-semibold")
                    return;
                }
                else {
                    if((response === 'saved') || response === ('updated')) {
                        $("#loader").html(`Trip successfully ${response}`).addClass("error", "font-weight-semibold");
                        window.location = pageReloadUrl
                    }
                    else {
                        if(response === "transporterNumberExists"){
                            $("#loader").html(`Transporter already exist. Two different transporters cannot share the same phone number.`).addClass("error");
                            return;
                        }
                        else if(response === "driversNumberExists"){
                            $("#loader").html(`Driver phone number already exist.`).addClass("error");
                            return;
                        }
                         
                    }
                }

            });
        }
        else{
            return false;
        }
    }
    

    $("#registerTrip").click(function(e) {
         e.preventDefault();
         if(validateGatedIn()===false) {return};
         submitTrip('/trips', '#frmTrips', '/update-trip');
    });

    $("#updateArrivalAtloadingBay").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        if(validateGatedIn()===false) { return }
        if(validateArrivalAtLoadingBay() === false) {return;};
        submitTrip(`/trips/${$id}`, '#frmTrips', '/update-trip');        
    });

    $("#updateLoadingInformation").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        if(validateGatedIn()===false) { return }
        if(validateArrivalAtLoadingBay()===false) { return };
        if(validateLoadingInformation()==false){return false};
        submitTrip(`/trips/${$id}`, '#frmTrips', '/update-trip'); 
    });

    $("#updateDeparture").click(function(e){
        e.preventDefault();
        $id = $("#id").val();
        $tripId = $("#tripId").val();
        if(validateGatedIn()===false) { return }
        if(validateArrivalAtLoadingBay()===false) { return };
        if(validateLoadingInformation()===false) { return };
        if(validateDeparture()===false){ return };
        submitTrip(`/trips/${$id}`, '#frmTrips', `/way-bill/${$tripId}/${$tripId}`);
    });

    

    $("#updateGatedOut").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        $tripId = $("#tripId").val();
        if(validateGatedIn()===false) { return }
        if(validateArrivalAtLoadingBay()===false) { return };
        if(validateLoadingInformation()===false) { return };
        if(validateDeparture()===false) { return };
        
        if(validateGatedOut() === false) { return };
        $("#loader").html('<i class="icon-spinner4 spinner mr-2"></i>Please Wait...').addClass('error');
        submitTrip(`/trips/${$id}`, '#frmTrips', '/update-trip');
    });


     /**
      * 
      * @param {*} domid Document Object Model ID to reference the status
      * @param {*} content Error or success content
      */

     function errorDisplay(domid, content, className) {
        $(domid).html(`<i class='icon-x'></i>${content}`).addClass(className).fadeIn(100).delay(3000).fadeOut(300);;
     }

     function validateGatedIn() {
        $gateIn = $("#gateIn").val();
         if($gateIn == "") {
             errorDisplay('#loader', 'The date and time for gate in is required.', 'error');
             return false;
         }
         $client = $("#clientId").val();
         if($client == "") {
             errorDisplay('#loader', 'Choose a client.', 'error');
             return false;
         }
         $loadingSite = $("#loadingSite").val();
         if($loadingSite == "") {
             errorDisplay('#loader', 'Choose a loading site', 'error');
             return false;
         }
         $truckId = $("#searchTruckNo").val();
         if($truckId == "") {
             errorDisplay('#loader', 'Enter the truck number or choose from the pop up list', 'error');
             return false;
         }
         $transporterId = $("#searchTransporter").val();
         if($transporterId == "") {
             errorDisplay('#loader', 'Enter transporter information or choose from the pop up list', 'error');
             return false;
         }
         
         $driverId = $("#searchDriver").val();
         if($driverId == "") {
             errorDisplay('#loader', 'Enter the driver information or choose from the pop up list', 'error');
             return false;
         }
         $productId = $("#productId").val();
         if($productId == "") {
             errorDisplay('#loader', 'Choose a product', 'error');
             return false;
         }
         $destinationState = $("#destinationState").val();
         if($destinationState == "") {
             errorDisplay('#loader', 'Choose destination state of the product.', 'error');
             return false;
         }
         $exactLocation = $("#exactLocation").val();
         if($exactLocation == "") {
             errorDisplay('#loader', 'Choose exact location where the product is headed.', 'error');
             return false;
         }
         return true;
    }

    function validateArrivalAtLoadingBay() {
        $tracker = $("#tracker").val();
        if($tracker == "") {
            errorDisplay('#loader', 'Please, choose the specific trip level above', 'error');
            return false;
        }
        $loadingBayArrival = $("#loadingBayArrival").val();
        if($loadingBayArrival == "") {
            errorDisplay('#loader', 'Arrival time and date is required for loading bay', 'error');
            return false;
        }
        return true;
    }

    function validateLoadingInformation() {
        $timeLoadingStart = $("#timeLoadingStart").val();
        if($timeLoadingStart == "") {
            errorDisplay('#loader', 'Time loading starts is required.', 'error');
            return false;
        }
        $timeLoadingEnd = $("#timeLoadingEnd").val();
        // if($timeLoadingEnd == "") {
        //     errorDisplay('#loader', 'Time loading ends is required.', 'error');
        //     return false;
        // }
        return true;
    }

    function validateDeparture() {
        $departure = $("#departure").val();
        if($departure == "") {
            errorDisplay('#loader', 'Loading bay departure time and date is required.', 'error');
            return false;
        }
        return true;
    }

    function validateGatedOut() {   

        $gatedOut = $("#gatedOut").val();
        if($gatedOut == "") {
            errorDisplay('#loader', 'Gated out time and date is required.', 'error');
            return false;
        }
        $customerName = $("#customerName").val();
        if($customerName == "") {
            errorDisplay('#loader', 'Customer\'s name is required.', 'error');
            return false;
        }
        $customerNumber = $("#customerNumber").val();
        if($customerNumber == "") {
            errorDisplay('#loader', 'Customer\'s phone number is required.', 'error');
            return false;
        }
        $loadedQuantity = $("#loadedQuantity").val();
        if($loadedQuantity == "") {
            errorDisplay('#loader', 'Loaded quantity is required.', 'error');
            return false;
        }
        $loadedWeight = $("#loadedWeight").val();
        if($loadedWeight == "") {
            errorDisplay('#loader', 'Loaded weight is required.', 'error');
            return false;
        }
        $customerAddress = $("#customerAddress").val();
        if($customerAddress == "") {
            errorDisplay('#loader', 'Customer\'s address is required.', 'error');
            return false;
        } 
    }

    function loader(placeholder, loadingtext, cssName) {
        $(placeholder).html(`<i class="icon-spinner2 spinner mr-2"></i>${loadingtext}`).addClass(cssName);
    }

    function receiver(placeholder, content) {
        $(placeholder).html(content);
    }

    // Void Trip
    $('.voidTrip').click(function(){
        $id = $(this).attr('id');
        $trip_id = $(this).attr('value');
        $ask = confirm('Are you sure you want to cancel this trip?');
        if($ask){
            $.post('/trip-cancel-order/'+$id, $('#frmCancelTrip').serializeArray(), function(data) {
                if(data == 'error'){
                    alert('Oops! Something went wrong while trying to void this trip, please contact the administrator!');
                    return
                }
                else{
                    if(data == 'voided'){
                        alert('You have successfully cancelled trip: '+$trip_id);
                        $url = '/view-orders';
                        window.location = $url;
                    }
                }
            });
        }   
        else{
            return false;
        }
    });

    $('#changeGateInTime').click(function(){
        $(this).addClass('hidden');
        $('#gateInPlaceholder').removeClass('hidden');
        $('#gateIn').css({fontSize:'10px'});
    });

    $('#changeArrivalTime').click(function(){
        $(this).addClass('hidden');
        $('#ArrivalPlaceholder').removeClass('hidden');
        $('#loadingBayArrival').css({fontSize:'10px'});
    });


    $('#changeGateOutTime').click(function(){
        $(this).addClass('hidden');
        $('#gateOutPlaceholder').removeClass('hidden');
        $('#gatedOut').css({fontSize:'10px'});
    });

    







   


});