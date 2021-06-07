$(function() {

    $("#addMore").click(function(e) {
        var mediumsize = '<div class="col-md-4 mb-2">';
        var close = '</div>';
        var formgroup = '<div class="form-group-4">';
        var sonumber_inputfield = `${mediumsize}${formgroup}<input type="text" name="sales_order_no[]" placeholder="S.O. Number" class="form-control salesOrderNumber" />${close}${close}`;

        var invoicenumber_input = `${mediumsize}${formgroup}<input type="text" name="invoice_no[]" placeholder="Invoice No." class="form-control invoiceNumber" />${close}${close}`;

        var waybillPhoto = `${mediumsize}${formgroup}<input type="file" name="photo[]" style="font-size:9px;" class="waybillChooser">`

        $(".input_field_wraps").append(`${sonumber_inputfield} ${invoicenumber_input} ${waybillPhoto}`)
    });

    function multiValidateFields(className, errorMessage) {
        $exit = true;
        $(className).each(function(i, v){
            $value = $(this).val();
            if($value === ""){
                alert(errorMessage);
                $exit = false;
            }
        });
        return $exit;
    }

    $("#addWaybillStatus").click(function(event) {
        event.preventDefault();
        if(multiValidateFields('.salesOrderNumber', 'You have at least one s.o. number to be entered.')==false){ return }
        if(multiValidateFields('.invoiceNumber', 'You have at least one invoice number to be entered.')==false){ return }
        if(multiValidateFields('.waybillChooser', 'You have at least one proof of waybill to be uploaded.')==false){ return }

        if(validateWaybill()==false){ return };
        $(this).html('<i class="spinner icon-spinner2"></i> Please wait...');
        $(this).prop("disabled", "disabled");
        $("#frmWayBill").submit();
        
    });


    $("#updateWayBillStatus").click(function() {
        $file = $("#file").val();
        $salesOrderNumber = $('.salesOrderNumber').val();
        $invoiceNumber = $('.invoiceNumber').val();

        if($file == "" && ($salesOrderNumber == "" || $invoiceNumber == "")) {
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
        
        $(this).html('<i class="spinner icon-spinner2"></i> Please wait...');
        $(this).prop("disabled", "disabled");
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

    // delete specific waybill details
    $('.deleteSpecificWaybill').click(function() {
        $id = $(this).attr("value");
        $user = $("#user_id").val();
        $ask = confirm('Are you sure you want to delete this waybill? ');
        if($ask) {
            $('#loader').html('<i class="spinner icon-spinner2"></i>Please wait...').addClass('success');
            $.get('/delete-specific-waybill/', {id:$id, user:$user}, function(data) {
                if(data == 'deleted') {
                    successful('#loader', 'waybill details removed successfully', 'success')
                    window.location = '';
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
    });

    $('.unlinkSignedWaybill').click(function() {
        $signedWaybillId = $(this).attr('id')
        $ask = confirm('Are you sure you want to remove this EIR? ')
        if($ask) {
            $('#waiter').html('<i class="spinner icon-spinner3"></i>Please wait...').addClass('font-size-xs')
            $.get('/unlink-signed-eir', { eirId: $signedWaybillId }, function(data) {
                if(data === 'unlinked') {
                    $('#waiter').html('EIR Deleted')
                    $url = ''
                    window.location = $url
                }
                else{
                    return false
                }
            })
        }
        else{
            return false
        }
    })

    $('.waybillType').click(function() { 
        $value = $(this).attr('value')
        $('#waybillType').val($value)
    })

    $('#addSignedWaybill').click(function($e) {
        $e.preventDefault()
        $waybillType = $('#waybillType').val()
        if($waybillType === "") {
            $('#waiter').html('Staus of waybill is required.').addClass('font-size-xs text-danger d-block')
            return false;
        }
        $signedWaybill = $('#signedWaybill').val()
        if(!$signedWaybill) {
            $('#waiter').html('Upload a signed '+$waybillType).addClass('font-size-xs text-danger d-block')
            $('#signedWaybill').focus()
            return false
        }
        $waybillNo = $('#waybillNo').val()
        if(!$waybillNo) {
            $('#waiter').html('The waybill no or container no is required.').addClass('font-size-xs text-danger d-block')
            $('#waybillNo').focus()
            return false;
        }
        $('#waiter').html('<i class="spinner icon-spinner3"></i>Please wait...').addClass('font-size-xs')
        $(this).attr('disabled', 'disabled')
        $('#frmUploadEir').submit();
    })

    $('#frmUploadEir').ajaxForm(function(data) {
        if(data === 'eirUploaded') {
            $('#waiter').html('<i class="icon-checkmark2"></i> Waybill uploaded successfully.').addClass('d-block')
            $url = ''
            window.location.href = $url
        }
    })

});