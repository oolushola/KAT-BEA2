$(function(){
    
    $("#addVatRate").click(function(e) {
        e.preventDefault();
        validateVatRate('/vat-rate');
    });

    $("#updateVatRate").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateVatRate(`/vat-rate/${$id}`);
    })

    function validateVatRate(url) {
        $("#loader").removeClass('error').html('');
        $withholdingtax = $("#withholdingtax").val();
            if($withholdingtax == "") {
                $("#loader").html('Withholding Tax is required.').addClass('error');
                $("#withholdingtax").focus();
                return;
            }
        $vat_rate = $("#vat_rate").val();
            if($vat_rate == "") {
                $("#loader").html('Vat Rate is required.').addClass('error');
                $("#withholdingtax").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmVatRate").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`Update client VAT criteria instead`);
                return false;
            }
            else {
                if(data === 'saved' || data == 'updated') {
                    $("#loader").html(`Product ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                    $pageReload = '/vat-rate';
                    window.location=$pageReload;
                }
                else{
                    return false;
                }
            }
        })
    }
})