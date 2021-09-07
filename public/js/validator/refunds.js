$(function() {
    $('#addPaymentVoucher').click(function($e) {
        // $(this).attr('disabled', 'disabled')
        $e.preventDefault();
        $('#frmPaymentVoucher').submit()
    })

    $('#frmPaymentVoucher').ajaxForm(function(data) {
        if(data == 'saved' || data == 'updated') {
            window.location = '';
        }
        else{
            $('#responsePlace').html('This record already exist. Update it instead.').addClass('error')
        }
    })


    $('#updatePaymentVoucher').click(function($e) { 
        $e.preventDefault();
        $id = $('#id').val();
        $.post('/payment-voucher-request/'+$id, $('#frmPaymentVoucher').serializeArray(), function(data) {
            if(data == 'updated') {
                window.location = '/payment-voucher-request/';
            }
            else{
                $('#responsePlace').html('This record already exist. Update it instead.').addClass('error')
            }
        })
    })


    $("#addMoreExpensesCategory").click(function(e) {
        var test = $("#moreExpenses").first().clone()
        $("#moreExpenses").last().after(test)
    })

    $(document).on('click', '.removeMoreExpenses', function() {
        $(this).parent('row').remove()
    })

    $('#checkAllPaymentVouchers').click(function() {
        $checked = $(this).is(':checked')
        if($checked) {
            $('.paymentVouchers').prop('checked', true)
        }
        else {
            $('.paymentVouchers').prop('checked', false)
        }
    })

    $('.paymentVouchers').click(function() {
        if($('.paymentVouchers').length == $('.paymentVouchers:checked').length) {
            $('#checkAllPaymentVouchers').prop('checked', true)
        }
        else{
            $('#checkAllPaymentVouchers').prop('checked', false)
        }
    })

    $('#verifyPaymentVoucher').click(function(e) {
        e.preventDefault();
       if($('.paymentVouchers:checked').length <= 0) {
           alert('C\'mon, you should at least select a payment voucher before stamping as verified')
           return false
       } 
       $e = $(this)    
       $e.html('<i class="icon-spinner2 spinner"></i> Verifying...').prop('disabled', true)       
       $.post('/verify-payment-voucher', $('#frmPaymentVoucher').serializeArray(), function(data) {
           if(data === 'accessDenied') {
                alert('Operation Aborted! You do not have permission to verify payments')
                return false
           }
           else {
               if(data === 'verified') {
                   alert('verification completed')
                   window.location = '';
               }
           }
       }) 
    })

    $('#approvePaymentVoucher').click(function() {
        $.get('/payment-voucher-approvals', {}, function(data) {
            $('#voucherApprovalListings').html(data)
        })
    })

    $('#approveAllPaymentVouchers').click(function() {
        $checked = $(this).is(':checked')
        if($checked) {
            $('.paymentVouchers').prop('checked', true)
        }
        else {
            $('.paymentVouchers').prop('checked', false)
        }
    })

    $(document).on('click', '.paymentVouchers', function() {
        if($('.paymentVouchers').length == $('.paymentVouchers:checked').length) {
            $('#approveAllPaymentVouchers').prop('checked', true)
        }
        else{
            $('#approveAllPaymentVouchers').prop('checked', false)
        }
    })

    $(document).on('click', '#approveVerifiedPayment', function(e) {
        e.preventDefault();
       if($('.paymentVouchers:checked').length <= 0) {
           alert('Hi Boss! You need to select at least one voucher.')
           return false
        } 
       
       $e = $(this)    
       $e.html('<i class="icon-spinner4 spinner"></i> Validating Approval...').prop('disabled', true)       
       $.post('/approve-payment-voucher', $('#frmApprovePaymentVoucher').serializeArray(), function(data) {
           if(data === 'cantUpdate') {
                alert('Operation Aborted! You do not have permission to approve payments')
                return false
           }
           else {
               if(data === 'approved') {
                   window.location = '';
               }
           }
       }) 
    })


    $('#checkAllVoucherUploads').click(function() {
        $checked = $(this).is(':checked')
        if($checked) {
            $('.paymentVoucherUploads').prop('checked', true)
        }
        else {
            $('.paymentVoucherUploads').prop('checked', false)
        }
    })

    $('.paymentVoucherUploads').click(function() {
        if($('.paymentVoucherUploads').length == $('.paymentVoucherUploads:checked').length) {
            $('#checkAllVoucherUploads').prop('checked', true)
        }
        else{
            $('#checkAllVoucherUploads').prop('checked', false)
        }
    })

    $(document).on('click', '#uploadPaymentVouchers', function(e) {
        e.preventDefault();
       if($('.paymentVoucherUploads:checked').length <= 0) {
           alert('Stop It! You need to select at least one voucher.')
           return false
       } 
       
       $e = $(this)    
       $e.html('<i class="icon-spinner3 spinner"></i> Uploading...').prop('disabled', true)       
       $.post('/upload-payment-voucher', $('#frmUploadPaymentVoucher').serializeArray(), function(data) {
           if(data === 'cantUpdate') {
                alert('Operation Aborted! You do not have permission to approve payments')
                return false
           }
           else {
               if(data === 'uploaded') {
                   window.location = '';
               }
           }
       }) 
    })

    $('#pendingUploads').click(function() {
        $checkStatus = $('#checkStatus').val();
        if($checkStatus == 1) {
            $('#availableUploads').addClass('col-md-6').removeClass('d-none')
            $('#defaultView').removeClass('col-md-12').addClass('col-md-6')
            $('#checkStatus').val(0)
        }
        else {
            if($checkStatus == 0) {
                $('#availableUploads').addClass('d-none')
                $('#defaultView').removeClass('col-md-6').addClass('col-md-12')
                $('#checkStatus').val(1)
            }
        }
    })

    $(document).on('click', '.paymentBreakdown', function() {
        $voucherId = $(this).attr("id")
        $toggleValue = $(this).attr('value')
        if($toggleValue == 0) {
            $('.voucher'+$voucherId).removeClass('d-none')
            $(this).attr('value', 1)
        }
        else{
            if($toggleValue == 1) {
                $('.voucher'+$voucherId).addClass('d-none')
                $(this).attr('value', 0)
                }
        }       
    })

    $(document).on('click', '.declineOneVoucher', function() {
        $voucherId = $(this).attr('value')
        $uniqueId = $(this).attr('title')
        $.get('/deline-payment-voucher', {declineStatus: 0, voucherId: $voucherId }, function(data) {
            if(data === 'declined') {
                alert('This payment has been declined.')
                $(`#parent${$uniqueId}`).addClass('d-none')
            }
        })
    })

    $(document).on('click', '#declineAllVerifiedPayments', function() {
        if($('.paymentVouchers:checked').length <= 0) {
            alert('Hi Boss! You need to select at least one voucher.')
            return false
        } 
        $ask = confirm('Do you really want to cancel all vouchers? ')
        if($ask) {
            $(this).attr('disabled', 'disabled').html('Please wait... <i class="icon-spinner2 spinner"></i>')
            $.get('/deline-payment-voucher', $('#frmApprovePaymentVoucher').serializeArray(), function(data) {
                if(data === 'declined') {
                    alert('This payment has been declined.')
                    window.location = ''
                }
            })
            //frmApprovePaymentVoucher
        }
    })


    //Decline a voucher!
    $('.declinePayment').click(function() {
        $id = $(this).attr('id')
        $ask = confirm('Are you sure you want to delcine this voucher?')
        if($ask){
            $(this).html('<i class="icon-spinner2 spinner"></i>Declining...')
            $.get('/flag-voucher', { id: $id }, function(data) {
                if(data === 'cancelled') {
                    $('#closeVoucher'+$id).addClass('d-none')
                }
                else {
                    alert('Oops! something went wrong.')
                }
            })
        }
        else {
            return false
        }
    })

    //delete a voucher
    $('.trashVoucher').click(function() {
        $id = $(this).attr('id')
        $('#closeVoucher'+$id).addClass('d-none')
        $.get('/delete-voucher-request', { id: $id }, function(data) {
            if(data == 'deleted') {
                alert('Voucher Deleted.')
                $('#closeVoucher'+$id).addClass('d-none')
                return false;
            }
        })
    })


    //add beneficiary
    $(".addBeneficiary").click(function() {
        $firstName = $("#firstName").val();
        if(!$firstName) {
            $("#firstName").focus();
            $("#dataDropper").html("Beneficiary first name is required")
            return false
        }
        $lastName = $("#lastName").val();
        $bankName = $("#bankName").val();
        if(!$bankName) {
            $("#bankName").focus();
            $("#dataDropper").html("Beneficiary bank name is required")
            return false
        }
        $accountName = $("#accountName").val();
        if(!$accountName) {
            $("#accountName").focus();
            $("#dataDropper").html("Beneficiary account name is required")
            return false
        }
        $accountNo = $("#accountNo").val();
        if(!$accountNo) {
            $("#accountNo").focus();
            $("#dataDropper").html("Beneficiary account number is required")
            return false
        }
        $(this).attr("disabled", "disabled").html('<i class="spinner icon-spinner2"></i> Please wait...') 
        $el = $(this)
        $.post("/add-refund-beneficiary", $("#frmBeneficiary").serializeArray(), function(data) {
            if(data === "saved") {
                window.location = '';
            }
            else {
                if(data === "recordExists") {
                    alert('Aborted! A user with the account number already exists!')
                    $el.removeAttr("disabled").html('Add New Beneficiary') 
                }
            }
        })

    });

    $('.useFirstAndLastName').click(function() {
        const checked = $(this).is(":checked");
        if(checked) {
            const accountName = $("#firstName").val()+' '+$('#lastName').val()
            $("#accountName").val(accountName)
        }
        else{
            $("#accountName").val("")
        }
    })
})

