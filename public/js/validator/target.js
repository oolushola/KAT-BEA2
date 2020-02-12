$(function(){
    $("#addTarget").click(function(e) {
        e.preventDefault();
        validateMonthlyTarget('/kaya-target');
    });

    $("#updateTarget").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateMonthlyTarget(`/kaya-target/${$id}`);
    })

    function validateMonthlyTarget(url) {
        $("#loader").removeClass('error').html('');
        $target = $("#monthlyTarget").val();
            if($target == "") {
                $("#loader").html('Target value is required.').addClass('error');
                $("#target").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmTarget").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`Target for this month already exists.`);
                return false;
            }
            else {
                if(data === 'saved' || data == 'updated') {
                    $("#loader").html(`Target for the month ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(5000);
                    $pageReload = '/kaya-target';
                    window.location=$pageReload;
                }
            }
        })
    }

})