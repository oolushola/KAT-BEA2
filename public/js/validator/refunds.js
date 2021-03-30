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


    $('#addMoreExpensesCategory').click(function(event) {
        $addMoreExpenses = '<div class="col-md-3"><div class="form-group mt-2">';
        $addMoreExpenses += '<input type="text" name="description[]" class="form-control" placeholder="Description">';
        $addMoreExpenses += '</div></div>';

        $addMoreExpenses += '<div class="col-md-3 mt-1"><div class="form-group" style="margin:0; padding:0">';
        $addMoreExpenses += '<input type="text" class="form-control" placeholder="Owner" name="owner[]">';
        $addMoreExpenses += '</div></div>';

        $addMoreExpenses += '<div class="col-md-3"><div class="form-group mt-1" style="margin:0; padding:0">';
        $addMoreExpenses += '<input type="text"  class="form-control" placeholder="Amount" name="amount[]">';
        $addMoreExpenses += '</div></div>';

        $addMoreExpenses += '<div class="col-md-3"><div class="form-group mb-1" style="margin:0; padding:0">';
        $addMoreExpenses += '<input type="file" placeholder="Amount" class="mt-2" name="attachment[]" style="font-size:10px">';
        $addMoreExpenses += '</div></div>';
        
        
        $('#moreExpenses').append($addMoreExpenses)
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
})

