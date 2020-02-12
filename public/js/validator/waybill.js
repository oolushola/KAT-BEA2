$(function() {

    $("#addMore").click(function(e) {
        var mediumsize = '<div class="col-md-6">';
        var close = '</div>';
        var formgroup = '<div class="form-group">';
        var sonumber_inputfield = `${mediumsize}${formgroup}<input type="text" name="sales_order_no[]" placeholder="S.O. Number" class="form-control salesOrderNumber" />${close}${close}`;

        var invoicenumber_input = `${mediumsize}${formgroup}<input type="text" name="invoice_no[]" placeholder="Invoice Number" class="form-control"    />${close}${close}`;
        $(".input_field_wraps").append(`${sonumber_inputfield}${invoicenumber_input}`)
    });

    $("#addWaybillStatus").click(function(event) {
        event.preventDefault();
        if(validateWaybill()==false){ return };
        $("#frmWayBill").submit();
    });

    $("#updateWayBillStatus").click(function() {
        $file = $("#file").val();
        if($file == "") {
            errorMessage(
                '#loader',
                'Choose the waybill to be uploaded',
                'error'
            )
            return false;
        }else{
            if($file !== '') {
                var ftype = $("#ftype").val();
                validateCsv(ftype);
                var filecheck = $("#filecheck").val();
                if(filecheck == "0"){return false;}
            }
        }
        successful(
            '#loader',
            `<i class="icon-spinner2 spinner mr-2"></i>Please Wait...`,
            'success'
        )
        $("#frmWayBill").submit();
    })

    

    function validateWaybill() {
        $tracker = $("#tracker").val();
        if($tracker < 4) {
            errorMessage(
                '#loader', 
                'Sorry, you cannot upload a waybill for this yet. It hasnt loaded and departed the loading bay.', 
                'error'
            )
            return false;
        }
        return true;
    }

    function errorMessage(placeholder, message, className) {
        $(placeholder).html(`<i class='icon-x'></i>${message}`).addClass(className).fadeIn(3000).delay(3000).fadeOut(2000);
    }

    function successful(placeholder, message, className) {
        $(placeholder).html(`${message}`).addClass(className);
    }

    $("#approveWaybill").click(function() {
        $checked = $(this).is(":checked");
        $waybill_id = $(this).attr("value");
        if($checked) {
            $("#tracker").val(9);
            $ask = confirm('Are you satisfied with this waybill?');
            if($ask) {
                successful(
                    '#loader',
                    `<i class="icon-spinner2 spinner mr-2"></i>Please Wait...`,
                    'success'
                )
                $tracker = $("#tracker").val();
                $.post(`/approve-waybill/${$waybill_id}`, $("#frmWayBill").serializeArray(), function(data) {
                    if(data == "approved") {
                        successful(
                            '#loader',
                            `Waybill has been successfully ${data}`,
                            'success'
                        );
                        window.location = '';
                    }
                })
            }
            else{
                return false;
            }
        }
        else{
            $("#tracker").val(8);
        }
    });

    $(".waybillStatus").click(function() {
        $("#defaultButton").addClass("hidden");
        $("#waybillRemark").addClass("show").removeClass("hidden");
        $value = $(this).attr("value");
        if($value == 0) {
            $("#withHolderContainer").addClass('show').removeClass('hidden');
            $("#statusChecker").val($value);
        }
        else{
            $("#withHolderContainer").addClass('hidden');
            $("#statusChecker").val($value);
        }
    });

    $("#addWaybillRemark").click(function(e) {
        e.preventDefault();
        $statusChecker = $("#statusChecker").val();
        if($statusChecker == 0) {
            $comment = $("#comment").val();
            if($comment == "") {
                errorMessage(
                    '#loader2',
                    'Please, who is with the waybill?',
                    'error'
                )
                return
            }
        }

        $.post('/waybill-remarks', $("#frmWayBill").serializeArray(), function(data) {
            if(data == "saved") {
                successful(
                    '#loader2',
                    'Your remark is noted. Thanks.',
                    'success'
                )
                window.location = '';
            }
            else{
                return;
            }
        })
    })


    $("#frmWayBill").ajaxForm(function(data){
        if(data == 'exists'){
            errorMessage(
                '#loader',
                'This waybill already exists.',
                'error'
            )
            return false;
        }
        else {
            if(data == 'saved' || data == 'updated') {
                successful(
                    '#loader',
                    `Waybill has been successfully ${data}`,
                    'success'
                );
                window.location = '';
            }
        }
    })
});