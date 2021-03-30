$(function() {
    $("#client").change(function() {
        $client_id = $("#client").val();
        $("#contentLoader").html('<i class="icon-spinner4 spinner"></i> Loading...').css({
            padding:'10px'
        });
        $('#invoiceSearchBox').removeClass('hidden');
        $.get(
            '/invoice-by-client', {client_id: $client_id}, function(data) {
               $('#contentLoader').html(data);
            }
        )
    });


    $("#invoiceSearchBox").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#searchAvailableInvoices tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });

    //check incentive
    $(document).on('click', '#addIncentive', function() {
        $actual = $(this).is(":checked");
        if($actual) {
            $('#proceedWithIntencive').removeClass('hidden');
            $('#invoiceTrip').addClass('hidden');
            $('#frmClientInvoice').attr('action', 'invoice-incentives');
            
            
        } else {
            $('#proceedWithIntencive').addClass('hidden');
            $('#invoiceTrip').removeClass('hidden');
            $('.invoiceActionHolder').removeClass('hidden');
            $('#frmClientInvoice').attr('action', 'invoice');
        }
    });

    $("#completeInvoice").click(function(e) {
        e.preventDefault();
        $("#loader").html(
            "<i class='icon-spinner2 spinner'></i> Preparing Invoice"
        ).delay(3000).fadeOut(2000);

        $.post(
            '/complete-invoicing', $("#frmCompleteInvoice").serializeArray(), function(data) {
                try {
                    window.setTimeout(function() {
                        if(data == 'completed'){
                            $("#saveAndPrintContainer").addClass('show').removeClass('hidden');
                            $("#completeInvoice").addClass('hidden');
                            $('#loader').html(
                                '<i class="icon-checkmark2"></i> Invoice Completed'
                            ).delay(3000).fadeOut(2000);
                        }
                    }, 2000);
                    $response = data.split('`');
                    if($response[0] === 'completed'){
                        window.location.href = `/invoice-trip/${$response[1]}`;
                    }
                } catch (error) {
                    return error;
                }
            }
        )
    });

    $('#paidInvoices').click(function(e) { 
        e.preventDefault();
        $('#acknowledgeChecker').val(1);
        $("#loader").html(
            "<i class='icon-spinner2 spinner'></i> Updating Invoice No"
        ).delay(3000).fadeOut(2000);
        $.post('/paid-invoices', $('#frmPaidInvoices').serializeArray(), function(data) {
            if(data == "updated") {
                $('#loader').html(
                    '<i class="icon-checkmark2"></i>Successful.'
                ).delay(3000).fadeOut(2000);

            window.location = '';
            }
        });
    });

    $('#acknowledgedInvoices').click(function(e) { 
        e.preventDefault();
        $('#acknowledgeChecker').val(2);
        $("#loader").html(
            "<i class='icon-spinner2 spinner'></i>Processing..."
        ).delay(3000).fadeOut(2000);
        $.post('/paid-invoices', $('#frmPaidInvoices').serializeArray(), function(data) {
            if(data == "updated") {
                $('#loader').html(
                    '<i class="icon-checkmark2"></i>Successful.'
                ).delay(3000).fadeOut(2000);

            window.location = '';
            }
        });
    });




    $("#addMore").click(function(e) {
        var mediumsize = '<div class="col-md-6">';
        var close = '</div>';
        var formgroup = '<div class="form-group">';
        var sonumber_inputfield = `${mediumsize}${formgroup}<input type="text" name="sales_order_no[]" placeholder="S.O. Number" class="form-control salesOrderNumber" />${close}${close}`;

        $(".input_field_wraps").append(`${sonumber_inputfield}`)
    });


    $('#searchInvoiceBank').click(function(e) {
        e.preventDefault();
        $.get('/multi-search', $('#frmMultipleInvoiceSearch').serializeArray(), function(data) {
            $('#contentHolder').html(data);
        });
    });

    $("#filterByBulkInvoiceStatus").on("change", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $(document).on('click', '.paymentCriteria', function() {
        $checked = $(this).is(":checked");
        if($checked) {
            $(this).next().removeClass('hidden');
        } else{
            $(this).next().addClass('hidden');
        }
    });


    $(document).on('click', '.acknowledgment', function() {
        $checked = $(this).is(":checked");
        if($checked) {
            $(this).next().removeClass('hidden');
        } else{
            $(this).next().addClass('hidden');
        }
    });

   
    $('.removeIncentive').click(function(){
        $id = $(this).attr('id');
        $ask = confirm('Are you sure you want to remove this incentive? ');
        if($ask) {
            $.post('/remove-incentive/'+$id, $('#frmReprintInvoice').serializeArray(), function(data) {
                if(data == 'removed'){
                    window.location.href="";
                } 
                else{
                    return false;
                }
            })
        }
        else{
            return false;
        }
    });


    $('.deleteInvoice').click(function() {
        $invoiceNumber = $(this).attr('value');
        $ask = confirm('Are you sure you want to delete INVOICE: '+$invoiceNumber);
        if($ask) {
            $.post('/delete-invoice/'+$invoiceNumber, $('#frmReprintInvoice').serializeArray(), function(data) {
                if(data == 'cant_delete') {
                    alert('Please remove all incentives attached to this invoice before delete.');
                    return false;
                }
                else{
                    if(data == 'deleted'){
                        alert($invoiceNumber+' invoice has been deleted successfully.');
                        window.location.href='/all-invoiced-trips';

                    }
                }
            })
        }
        else{
            return false;
        }

    });

    $(".cancelAcknowledgment").click(function() {
        $id = $(this).attr("id");
        sendToServer('/cancel-acknowledgment', $id, '#loader', 'Acknowledgment Cancelled.')
        
    });

    $(".cancelPayment").click(function() {
        $id = $(this).attr("id");
        sendToServer('/remove-payment', $id, '#loader', 'Payment removed.')
        
    });

    function sendToServer(uri, id, placeholder, successMessage){
        $.get(uri, {value:id}, function(data) {
            if(data === 'removed') {
                $(placeholder).html(successMessage);
                $uri = '';
                window.location = $uri;
            }
            else{
                $(placeholder).html('Oops, something went wrong.')
                return false;
            }
        })
    }

    $('.removeSpecificTrip').click(function() {
        $trip_id = $(this).attr("id");
        $ask = confirm("Are you sure you want remove this trip? if done by mistake, it could be added by back.");
        if($ask) {
            $('#removeLoader').html('<i class="spinner icon-spinner"></i>Wait..').addClass("mb-2");
            $(this).parent().parent().remove();
            sendToServer('/remove-specific-trip-on-invoice', $trip_id, '#loader', 'Removed successfully.');    
        }
        else{
            return false;
        }
    });

    
    $('#specialRemarkChecker').click(function() {
        $checked = $(this).is(":checked");
        if($checked){
            $('.descriptor-label').addClass('hidden');
            $('.descriptor').removeClass('hidden');
        }
        else{
            $('.descriptor-label').removeClass('hidden');
            $('.descriptor').addClass('hidden');
        }
    });

    //send to save special remark
    $('#saveSpecialRemark').click(function(e) {
        e.preventDefault();
        $condition = $('#condition').val();
        if($condition == "") {
            $('#condition').focus();
            return false;
        }

        $description = $('#description').val();
        if($description == "") {
            $('#description').focus();
            return false;
        }

        $amount = $('#amount').val();
        if($amount == "") {
            $('#amount').focus();
            return false;
        }

        $.post('/add-special-remark', $('#frmReprintInvoice').serializeArray(), function(data){
            if(data == 'saved'){
                window.location = '';
            }
            else{
                return false;
            }
        })

    })


    //Quick Preview of an invoice
    $('.invoicePreview').click(function() {
        $invoiceNo = $(this).attr("id");
        $completedInvoiceNo = $(this).attr('value')
        $paymentStatus = $(this).attr('data-payment')
        $acknowledgment = $(this).attr('data-acknowledgement')
        $('#invoiceNoPlaceholder').html(`INVOICE NO: ${ $completedInvoiceNo }`)
        $('#fullInvoiceNo').val($completedInvoiceNo);
        $('#invoiceQuickViewController').html('<i class="icon-spinner3 spinner"></i>Please wait, fetching details...').addClass('font-weight-semibold')

        $.get('/invoice-preview', { invoice_no: $invoiceNo, payment_status: $paymentStatus, acknowledgement: $acknowledgment }, function(data) {
            $('#invoiceQuickViewController').html(data);
        })  
    })

    $(document).on('dblclick', '.initialRatePlaceholder', function() {
        $id = $(this).attr('id')
        $tripId = $(this).attr("value")
        $(this).addClass('d-none')
        $('#amountPaid'+$tripId).removeClass('d-none')
        $('#incentive'+$tripId).addClass('d-none')

        $(document).on("keyup", '#amountPaid'+$tripId, function(e) {
            if(e.keyCode === 13) {
                $('#loader'+$tripId).html('<i class="icon-spinner3 spinner">')
                $value = eval($(this).val())
                $.get('/update-amount-paid', {id: $id, amount_paid: $value }, function(data) {
                    if(data == "updated") {
                        $('#loader'+$tripId).html('<i class="icon-checkmark2">')
                    }
                })
            }
        })
    })

    $(document).on('click', '.paidChecker', function() { 
        $checker = $(this).is(":checked");
        if($checker) {
            $('#paidDateChecker').removeClass('d-none')
            $('#paidDateChecker').val('');
            $('#changeInvoiceStatus').addClass('d-none')
            $('#paymentTypeHolder').removeClass('d-none')
            $('#partPaymentBtn').removeClass('d-none')
        }
        else {
            $('#paidDateChecker').addClass('d-none')
            $('#changeInvoiceStatus').removeClass('d-none')
            $('#paymentTypeHolder').addClass('d-none')
            $('#partPaymentBtn').addClass('d-none')
        }
    })

    //amountPaid initialRatePlaceholder

    $(document).on('click', '.paymentType', function() {
        $value = $(this).attr('value');
        if($value == 0) {
            $('#partPaymentCompleted').removeClass('d-none')
            $('.initialRatePlaceholder').addClass('d-none')
            $('.amountPaid').removeClass('d-none')
        }
        else{
            $('#partPaymentCompleted').addClass('d-none')
            $('.initialRatePlaceholder').removeClass('d-none')
            $('.amountPaid').addClass('d-none')
        }
        $('#paymentType').val($value);
    })

    $(document).on('click', '#updatePayment', function(e) {
        e.preventDefault();
        $paidDateChecker = $('#paidDateChecker').val()
        if($paidDateChecker == '') {
            return false;
        }
        $paymentTypeHolder = $('#paymentType').val()
        if($paymentTypeHolder == "") {
            return false;
        }
        $('#paidPlaceholder').html('<i class="icon-spinner3 spinner"></i>')
        $.get('/paid-invoices', $('#frmUpdatePayment').serializeArray(), function(data) {
            if(data == "updated") {
                window.location.href=''
                // $event.addClass('d-none')
                // $('#paymentState').html('<i class="icon-checkmark4 text-success"></i>')
                // $('.paidChecker').attr('disabled', 'disabled')
                // $('#paidPlaceholder').html('')
            }
            else{
                return false
            }
        })
    })

    // $(document).on('keyup', '#paidDateChecker', function(e) {
    //     $paidDate = $(this).val()
    //     $paymentType = $('#paymentType').val();
    //     $invoiceNo = $(this).attr('title')
    //     $event = $(this)
    //     if(e.keyCode === 13) {
    //         if($paymentType == 0) {
    //             return false
    //         }
    //         $('#paidPlaceholder').html('<i class="icon-spinner3 spinner"></i>')
    //         $.get('/paid-invoices', { date_paid: $paidDate, invoice_no: $invoiceNo, checker: 1, paymentType: $paymentType }, function(data) {
    //             if(data == "updated") {
    //                 $event.addClass('d-none')
    //                 $('#paymentState').html('<i class="icon-checkmark4 text-success"></i>')
    //                 $('.paidChecker').attr('disabled', 'disabled')
    //                 $('#paidPlaceholder').html('')
        
    //             }
    //             else{
    //                 return false
    //             }
    //         })
    //     }
    // })

    $(document).on('click', '.acknowledgementChecker', function() {
        $checker = $(this).is(":checked");
        if($checker) {
            $('#acknowledgementDateChecker').removeClass('d-none')
            $('#acknowledgementDateChecker').val('');
        }
        else {
            $('#acknowledgementDateChecker').addClass('d-none')
        }
    })

    $(document).on('keyup', '#acknowledgementDateChecker', function(e) {
        $acknowledgementDate = $(this).val()
        $invoiceNo = $(this).attr('name')
        $event = $(this)
        if(e.keyCode === 13) {
            $('#acknowledgmentPlaceholder').html('<i class="icon-spinner3 spinner"></i>')
            $.get('/paid-invoices', { acknowledgement_date: $acknowledgementDate, invoice_no: $invoiceNo, checker: 2 }, function(data) {
                if(data == "updated") {
                    $event.addClass('d-none')
                    $('#acknowledgmentState').html('<i class="icon-checkmark4 text-success"></i>')
                    $('.acknowledgementChecker').attr('disabled', 'disabled')
                    $('#acknowledgmentPlaceholder').html('')
                    $('.paidChecker').removeClass('d-none')
                }
                else{
                    return false
                }
            })
        }
    })

    $(document).on('click', '#viewPaymentHistory', function(){
        $invoiceNo = $(this).attr('value')
        $('#paymentHistoryLoader').html('<i class="spinner icon-spinner3 ml-2"></i>')
        $.get('/invoice-payment-history', {invoice_no: $invoiceNo}, function(data) {
            $('#paymentHistoryLoader').html(data)
        })
    })

})