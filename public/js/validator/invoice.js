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

})