
$(function(){
    $("#addBulkPayment").click(function(e) {
        e.preventDefault();
        validateChunkRate('/bulk-payment');
    });

    $("#updateBulkPayment").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateChunkRate(`/bulk-payment/${$id}`);
    });


    $("#updateTransporter").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateTransporter(`/bulk-payment/${$id}`);
    });

    $("#approveBulkPayment").click(function(e) {
        e.preventDefault();
        $ask = confirm('Are all payments truly approved?');
        if($ask) {
            $.post("/approvebulkpayment", $("#frmApproveChunkRate").serializeArray(), function(response) {
                if(response == "approved") {
                    alert('Payments has been approved.');
                    window.location = '';
                }
            });
        }
        else{
            return false;
        }
    });

    function validateChunkRate(url) {
        $("#loader").removeClass('error').html('');
        $transporterId = $("#transporterId").val();
            if($transporterId == "0") {
                $("#loader").html('Choose a transporter to be credited').addClass('error');
                return;
            }
        $amountCredited = $("#amountCredited").val();
            if($amountCredited == '') {
                $("#loader").html('Amount to be credited is required.').addClass('error');
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmBulkPayment").serializeArray(), function(data) {
            if(data === 'cant_proceed') {
                $("#loader").html(`Process Denied! You still have a pending payment waiting to be approved for this client.`);
                return false;
            }
            else {
                if((data === 'saved') || (data == 'updated')) {
                    $("#loader").html(`Chunk payment request sent`).addClass('error').fadeIn(3000).fadeOut(5000);
                    setTimeout(function(){
                        $pageReload = '/bulk-payment';
                        window.location=$pageReload;
                    }, 3000);
                }
            }
        })
    }

    


    function submit(url, frmname, reloader, responseDesc) {
        $.post(url, $(frmname).serializeArray(), function(response) {
            if(response == 'approved'){
                $("#advanceLoader").html('Thank you for your confirmation.')
                $pageReload = reloader;
                window.location=$pageReload;
            }
            else{
                if(response == 'deleted') {
                    $("#advanceLoader").html('You have successfully decline this payment request. This stops at your desk.');
                    $pageReload = reloader;
                    window.location=$pageReload
                }
            }
        });
    }

    // Initiate payment for advance
    $(".initiatePayment").click(function() {
        $advance_payment_id = $(this).attr("value");
        $advanceStatus = $(this).attr('name');
        $('#advanceLoader').html('<i class="icon-spinner4 spinner"></i> Initiating...').fadeIn(1000).delay(3000).fadeOut(1000);
        window.setTimeout(function() {
            submit(`/initiate-payment/${$advance_payment_id}`, '#frmPayment', '/payment-request');
            $("#advanceLoader").html('<i class="icon-checkmark3"></i>Payment Initiated.').delay(2000);
        }, 3000);
    });


    // Initiate Payment for Balance
    $('.initiateBalancePayment').click(function() {
        $balance_payment_id = $(this).attr("value");
        $balanceStatus = $(this).attr('name');
        initiatePayment('#balanceLoader', `/initiate-balance/${$balance_payment_id}`, '#frmPayment', '/payment-request');
    })


    function initiatePayment(placeholder, server, formName, url) {
        $(placeholder).html('<i class="icon-spinner4 spinner"></i> Initiating...').fadeIn(1000).delay(3000).fadeOut(1000);
        window.setTimeout(function() {
            submit(`${server}`, formName, url);
            $(placeholder).html('<i class="icon-checkmark3"></i>Payment Initiated.').delay(2000);
        }, 3000);
    }

    // Approve advance payment
    $("#approveAdvancePayment").click(function(e) {
        e.preventDefault();
        $('#advanceLoader').html('<i class="icon-spinner3 spinner"></i> Processing...');
        submit('/approve-advance-payment', '#frmPayment', '/payment-request');
    });
    
    $(".except").click(function(){ 
        $amount = $(this).attr("value");
        $tripId = $(this).attr("role");
        $id = $(this).attr("id");
        $("#totalAmountHolder").val($amount);
        $("#totalAmount_").val($amount);
        $("#tripIdHolder").html($tripId);
        $("#payid").val($id);
        $('#actualAmount').val($amount);
    });

    $("#percentageDifference").click(function() {
        $("#percentHolder").removeClass("hidden");
        $("#payInFullContainer").addClass("hidden");
        $("#manualAdvancwPaymentHolder").addClass("hidden");

        $(this).addClass("exception-default");
        $("#fullPayment").removeClass("exception-default");
        $('#manualAdvanceInput').removeClass('exception-default');
        $("#advanceNavigator").val(2);
    })

    $("#fullPayment").click(function() {
        $("#payInFullContainer").removeClass("hidden");
        $("#percentHolder").addClass("hidden");
        $("#manualAdvancwPaymentHolder").addClass("hidden");

        $(this).addClass("exception-default");
        $("#percentageDifference").removeClass("exception-default");
        $('#manualAdvanceInput').removeClass('exception-default');
        $("#advanceNavigator").val(3);
    });

    $("#manualAdvanceInput").click(function() {
        $("#payInFullContainer").addClass("hidden");
        $("#percentHolder").addClass("hidden");
        $("#manualAdvancwPaymentHolder").removeClass("hidden");

        $(this).addClass("exception-default");
        $("#percentageDifference").removeClass("exception-default");
        $("#fullPayment").removeClass("exception-default");
        $("#advanceNavigator").val(4);
    });

    $("#newAdvanceRate").keyup(function() {
        $getTotalAmount = $("#totalAmountHolder").val();
        $newAdvanceRate = $("#newAdvanceRate").val();
        $expectedAdvanceRate = ($newAdvanceRate / 100) * $getTotalAmount;

        if($newAdvanceRate == "") {
            $("#advanceRatePreview").html('');
        }
        else{
            $("#advanceRatePreview").html('New Advance Rate : &#x20A6;'+$expectedAdvanceRate.toFixed(2)).addClass('success');
        }

        if($newAdvanceRate == "") {
            $("#newBalanceRate").val("");
            $("#balanceRatePreview").html("");
        } else {
            $balanceEquivalent = 100 - $newAdvanceRate;
            $("#newBalanceRate").val($balanceEquivalent);
            $balanceRate = $("#newBalanceRate").val();
            $expectedBalanceRate = $balanceRate / 100 * $getTotalAmount;
            $("#balanceRatePreview").html('New Balance Rate: &#x20A6;'+$expectedBalanceRate.toFixed(2)).addClass('success');
        }
    });


    $('#advanceTobePaid').keyup(function() {
        $actualAmount = $('#actualAmount').val();
        $enteredAmount = $(this).val();
        $expectedBalance = $actualAmount - $enteredAmount;
        $('#probableBabalance').val($expectedBalance);
    });

   
   

    
    $("#payInFull").click(function() {
        $checked = $(this).is(":checked");
        if($checked){
            $("#pay_in_full").val(1);
        } else {
            $("#pay_in_full").val(0);
        }
    });

    
    $("#updatePercentile").click(function(e) {
        e.preventDefault();
        $newAdvanceRate = $("#newAdvanceRate").val();
        if($newAdvanceRate == "") {
            $("#exceptionLoader").html("<i class=\"icon-x\"></i>Advance % Rate is Required.").addClass("error");
            return false;           
        }
        $percentileRemark = $("#percentileRemark").val();
        if($percentileRemark == "") {
            $("#exceptionLoader").html("<i class=\"icon-x\"></i> Please, state your reason in the remark/comment section").addClass("error");
            return false;           
        }
        $.post("/advance-exception", $("#frmAdvanceEception").serializeArray(), function(response) {
            if(response == 'updated'){
                $("#exceptionLoader").html('Payment Exception Recorded Successfully.');
                window.location="/payment-request";
            }
            else{
                $("#exceptionLoader").html('Oops! Something went wrong...');
                return false;
            }
        });
    })

    //pay in full
    $("#updateFullPayment").click(function(e) {
        e.preventDefault();
        $payinfull = $("#pay_in_full").val();
        if($payinfull == 0) {
            $("#exceptionLoader").html("<i class=\"icon-x\"></i> Click the checkbox to confirm for full payment.").addClass("error");
            return false;
        }
        $fullPaymentRemarks = $("#fullPaymentRemarks").val();
        if($fullPaymentRemarks == "") {
            $("#exceptionLoader").html("<i class=\"icon-x\"></i> A remark is necessary for full payment.").addClass("error");
            return false;
        }
        $.post("/advance-exception", $("#frmAdvanceEception").serializeArray(), function(response) {
            if(response == 'updated'){
                $("#exceptionLoader").html('Payment Exception Recorded Successfully.');
                window.location="/payment-request";
            }
            else{
                $("#exceptionLoader").html('Oops! Something went wrong...');
                return false;
            }
        });
    });

    $('#manualAmountProceed').click(function(e) {
        e.preventDefault();
        $advanceTobePaid = $('#advanceTobePaid').val();
        $probableBabalance = $('#probableBabalance').val();

        if($advanceTobePaid == '') {
            $("#exceptionLoader").html("<i class=\"icon-x\"></i> Advance rate to be paid is required.").addClass("error");
            $('#advanceTobePaid').focus();
            return false;
        }

        $("#exceptionLoader").html("<i class=\"spinner icon-spinner2\"></i> Please wait...").addClass("error");
        
        try {
            $.post('/advance-exception', $('#frmAdvanceEception').serializeArray(), function(data) {
                if(data == 'updated'){
                    $("#exceptionLoader").html('Advance record saved successfully.').addClass("success").fadeIn(1000).delay(3000).fadeOut(2000);
                }
            })
        } catch (error) {
            if(error) {
                $("#exceptionLoader").html("<i class=\"icon-x\"></i>"+error).addClass("error");
                return false;
            }
        }
    })

    // balance popup exception
    $(".balanceExcept").click(function() {
        $value = $(this).attr("value");
        $array = $value.split(',');
        $("#tripDestination").val($array[0]);
        $("#advancePaid").val($array[1]);
        $("#supposedBalance").val($array[2]);
        $("#transporterId").val($array[3]);
        $("#tripId").val($array[4]);
        
        $id = $(this).attr("id");
        $("#balanceTripId").val($id);
        $("#balanceTripIdHolder").html($array[4]);

        $('#actualBalanceAmount').val($array[2]);
    });

    $('#bitPartBalance').click(function(){
        $checked = $(this).is(':checked');
        if($checked){
            $('#wrongRouteHolder').addClass('hidden').removeClass('show');
            $('#bitPaymentHolder').addClass('show').removeClass('hidden');
            $('#balanceExceptionChecker').val(2)
        } else {
            $('#wrongRouteHolder').addClass('show').removeClass('hidden');
            $('#bitPaymentHolder').addClass('hidden').removeClass('show');
            $('#balanceExceptionChecker').val(1);
        }
    });

    $('#balancePartPayment').keyup(function() {
        $balanceTobePaid = $(this).val();
        $actualBalanceAmount = $('#actualBalanceAmount').val();
        $outstanding = Number($actualBalanceAmount - $balanceTobePaid);
        $('#outstandingBalance').val($outstanding);

    })

    // get states
    $("#stateId").on("change", function() {
        $stateId = $("#stateId").val();
        $("#transporterRate").val("");
        $("#exactLocationHolder").html('<i class="icon-spinner3 spinner"></i>Please wait...');
        $.get("/balance-exact-location", {regional_state_id : $stateId}, function(data) {
            $("#exactLocationHolder").html(data);
        });
    });

    // get amount
    $(document).on("change", "#exactLocationId", function() {
        $exactLocationId = $("#exactLocationId").val();
        $.get('/transporter-rate-amount', {exact_location_id: $exactLocationId}, function(data) {
            $("#transporterRate").val(data);
        })
    });

    // update the balance rate
    $("#updateBalanceException").click(function(e) {
        e.preventDefault();

        $ask = confirm("Please, confirm balance exception by clicking on 'OK' ");
        if($ask) {
            $.post('/balance-exception', $("#frmBalanceException").serializeArray(), function(data) {
                if(data == "updated") {
                    $("#exceptionBalanceLoader").html('Balance exception has been successfully update. Please note that this will after the DATABASE RECORD for this particular trip.');
                    window.location = '/payment-request';
                } else {
                    $("#exceptionBalanceLoader").html('Oops! Sorry, something went wrong!');
                    return false;
                }
            });
        }
        else{
            return false;
        }
    });

    $(".balanceInitiator").click(function() {
        $trip_id = $(this).attr("value");
        $id = $(this).attr("id");
        $("#tripIdofBalanceInitiate").val($trip_id);
        $("#balanceInitiateId").val($id);
    })

    $("#confirmCheck").click(function() {
        $checker = $(this).is(":checked");
        if($checker) {
            $("#confirmProceed").val(1)
        }
        else {
            $("#confirmProceed").val(0);
        }
    })

    //proceedBalancePayment
    $("#proceedBalancePayment").click(function(e) {
        e.preventDefault();
        $confirmProceed = $("#confirmProceed").val();
        if($confirmProceed === "0") {
            $("#waitLoader").html('<i class="icon-spinner3 spinner"></i> Confirm you need to proceed.').addClass('success').fadeIn(2000).delay(4000).fadeOut(2000);
            return false;
        } 

        $("#waitLoader").html('<i class="icon-spinner3 spinner"></i> Please wait while we verify the status of this  trip waybill').addClass('success').fadeIn(2000).delay(4000).fadeOut(2000);
        $trip_id = $("#tripIdofBalanceInitiate").val();
        $.post('/proceed-balance-initiation', $("#frmInitiateBalance").serialize(), function(data) {
            if(data == 'no_record') {
                $('#waitLoader').html("This trip has no waybill information recorded. Do you wish to proceed?");
                $("#proceedBalancePayment").html("Proceed Anyway!").addClass("btn-info");
                $("#proceedAnywayConfirmation").removeClass("hidden");
                $("#confirmProceed").val(0);
                return false
            }
            else if(data == 'not_received'){
                $('#waitLoader').html("This trip waybill has not gotten to HQ. Do you wish to proceed?");
                $("#confirmProceed").val(0);
                return false;
            }
            else {
                if(data == 'uploaded') {
                    $('#waitLoader').html("Balance request has been uploaded to finance");
                    window.location = '/payment-request';
                }
            }
        });
    });

    $('#updateBalanceRequest').click(function(e) {
        e.preventDefault();
        $actualBalanceAmount = $('#actualBalanceAmount').val();
        $balanceTobePaid = $('#balancePartPayment').val();
        $outstandingBalance = $('#outstandingBalance').val();

        if($balanceTobePaid === '') {
            $("#exceptionBalanceLoader").html('<i class="icon-x"></i>Balance Amount is required.').addClass('error').fadeIn(2000).delay(4000).fadeOut(2000);
            return false;
        }

        if(Number($balanceTobePaid) > Number($actualBalanceAmount)) {
            $("#exceptionBalanceLoader").html('<i class="icon-x"></i>Actual balance is lesser than what you want to pay?').addClass('error').fadeIn(2000).delay(4000).fadeOut(2000);
            return false;
        }

        $.post('/balance-exception', $("#frmBalanceException").serializeArray(), function(data) {
            if(data == "saved"){
                $("#exceptionBalanceLoader").html('<i class="icon-checkmark2"></i>Exception has been successfully registered.').addClass('success').fadeIn(2000).delay(4000).fadeOut(2000);
                window.location = '/payment-request';
            }
            else{
                $("#exceptionBalanceLoader").html('<i class="icon-x"></i>Server Error!').addClass('error').fadeIn(2000).delay(4000).fadeOut(2000);
                return false;
            }
        });

    })

    // approve balance payment
    $("#approveBalancePayment").click(function(e) {
        e.preventDefault();
        $("#balanceLoader").html('<i class="icon-spinner3 spinner"></i>Please wait...').delay(4000).fadeOut(2000);
        $.post('/approve-balance', $("#frmPayment").serializeArray(), function(data) {
            if(data == "approved") {
                $("#balanceLoader").html('<i class="icon-x">Payment(s) approved.').delay(2000).fadeOut(1000);
                window.location='/payment-request';
            }
            else {
                $("#balanceLoader").html('<i class="icon-x">Oops! something went wrong. If this persist after 5 minutes please contact Admin.').delay(4000).fadeOut(2000);
                return false
            }
        });
    })

    $('#payalloutstanding').click(function(){
        $check = $(this).is(':checked');
        if($check){
            $('.partPaymentofOutstanding').removeClass('hidden');
            $('#outstandBalanceChecker').val(2);
        } else {
            $('#outstandBalanceChecker').val(1);
            $('.partPaymentofOutstanding').addClass('hidden');
        }
    })

    $('.payoutstandingBalance').click(function(){
        $values = $(this).attr("value");
        $array = $values.split(',');
        $('#outstandingBalanceUpdate').val($array[0]);
        $("#outstandingBalanceTripId").val($array[1]);
        $('#outstandingBalanceId').val($(this).attr('id'));
    });

    //pay part payment of outstanding.
    $('#outstandingPartPayment').keyup(function(event) {
        $outstandingBalanceUpdate = $('#outstandingBalanceUpdate').val();        
        $remainingOutStanding = $outstandingBalanceUpdate - event.target.value;
        $('#newOutstanding').val($remainingOutStanding);
    });

    //update outstanding balance
    $('#updateOutstandingBalanceRequest').click(function(event) {
        event.preventDefault();
        if($('#outstandBalanceChecker').val() === 1) {
            $outstandingPartPayment = $('#outstandingPartPayment').val();
            if($outstandingPartPayment === '') {
                $("#outstandlingBalanceLoader").html('<i class="icon-x"></i>The outstading balance Amount is required.').addClass('error').fadeIn(2000).delay(4000).fadeOut(2000);
                return false;
            }
        }
        $('#outstandlingBalanceLoader').html('<i class="spinner icon-spinner3"></i>Please wait...').addClass('success');
        $.post('/update-outstanding-balance', $('#frmOutstandingBalance').serializeArray(), function(data) {
            if(data == 'saved') {
                $('#outstandlingBalanceLoader').html('Outstanding balance has been successfully updated.').addClass('success');
                window.location.href='';
            } else {
                return false;
            }
        });
    })


})