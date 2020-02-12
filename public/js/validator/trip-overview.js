$(function(){
    $("#requestForAdvancePayment").click(function(e) {
        e.preventDefault();
        loader('#loader', 'error', '<i class="icon-spinner2 spinner mr-2"></i>Your request is being processed...');
        setTimeout(() => {
            sender('/trip-overview-payment-request', '#frmPaymentRequest');
        }, 3000);
    });

    $("#requestForBalance").click(function(e) {
        e.preventDefault();
        loader('#loader', 'error', '<i class="icon-spinner2 spinner mr-2"></i>Your request for balance is being processed...');
        setTimeout(() => {
            sender('/trip-overview-payment-request', '#frmPaymentRequest');
        }, 3000);
    })

    function loader(placeholder, className, description) {
        $(placeholder).addClass(className).html(description).delay(5000);
    }

    function sender(url, formname) {
        $.post(url, $(formname).serializeArray(), function(response) {
            if(response === 'successful') {
                loader('#loader', 'success', '<i class="icon-ok"></i>Thank you! Your request will be handled by the finance team at HQ.');
                window.location = '';
            }
            else{
                loader('#loader', 'success', '<i class="icon-x"></i>Something went wrong. Reload and try again, if this persist after 10 minutes, please contact the HQ');
                window.location = '';
            }
        });
    }

})