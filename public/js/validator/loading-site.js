$(function(){
    
    $("#addLoadingSite").click(function(e) {
        e.preventDefault();
        validateLoadingSite('/loading-sites');
    });

    $("#updateLoadingSite").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateLoadingSite(`/loading-sites/${$id}`);
    })

    function validateLoadingSite(url) {
        $("#loader").removeClass('error').html('');
        $stateDomiciled = $("#stateDomiciled").val();
            if($stateDomiciled == 0) {
                $("#loader").html('State domiciled is required.').addClass('error');
                $("#stateDomiciled").addClass('element-error').focus();
                return;
            }
        $loadingSiteCode = $("#loadingSiteCode").val();
            if($loadingSiteCode == "") {
                $("#loader").html('Loading site code is required.   ').addClass('error');
                $("#loadingSiteCode").focus();
                return;
            }
        $loadingSite = $("#loadingSite").val();
            if($loadingSite == "") {
                $("#loader").html('Loading site is required.').addClass('error');
                $("#loadingSite").focus();
                return;
            }
        $address = $("#address").val();
            if($address == "") {
                $("#loader").html('Address is required.').addClass('error');
                $("#address").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
        $.post(url, $("#frmLoadingSite").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A loading site with "${$loadingSiteCode}" already exists for this state.`).addClass('error');
                return false;
            }
            else {
                if(data === 'saved' || 'updated') {
                    $("#loader").html(`Loading site ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                    $pageReload = '/loading-sites';
                    window.location=$pageReload;
                }
                else{
                    return false;
                }
            }
        })
    }
})