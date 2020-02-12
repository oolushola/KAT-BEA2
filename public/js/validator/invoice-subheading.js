$(function(){
    
    $("#addInvoiceHeading").click(function(e) {
        e.preventDefault();
        validateInvoiceSubheading('/invoice-subheading');
    });

    $("#updateInvoiceHeading").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateInvoiceSubheading(`/invoice-subheading/${$id}`);
    })

    function validateInvoiceSubheading(url) {
        $("#loader").removeClass('error').html('');
        $client = $("#clientId").val();
        if($client == '') {
            $("#loader").html('Client is required').addClass('error').fadeIn(3000).delay(2000).fadeOut(2000);;
            $("#client").addClass('element-error').focus();
            return;
        }

        $salesOrderNoHeader = $("#salesOrderNoHeader").val();
        if($salesOrderNoHeader == "") {
            $("#loader").html('Sales order number style is required.').addClass('error').fadeIn(3000).delay(2000).fadeOut(2000);
            $("#salesOrderNoHeader").focus();
            return;
        }

        $invoiceNumberHeader = $("#invoiceNumberHeader").val();
        if($invoiceNumberHeader == "") {
            $("#loader").html('Invoice number style is required.').addClass('error').fadeIn(3000).delay(2000).fadeOut(2000);
            $("#invoiceNumberHeader").focus();
            return;
        }

        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...')
        $.post(url, $("#frmSubheading").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`Invoice heading already added for this client" already exists.`).fadeIn(3000).delay(2000).fadeOut(2000);
                return false;
            }
            else {
                if(data === 'saved' || data == 'updated') {
                    $("#loader").html(`Invoice header ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                    $pageReload = '/invoice-subheading';
                    window.location=$pageReload;
                }
                else{
                    return false;
                }
            }
        })
    }
})