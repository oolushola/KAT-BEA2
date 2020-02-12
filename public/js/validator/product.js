$(function(){
    
    $("#addProduct").click(function(e) {
        e.preventDefault();
        validateProduct('/products');
    });

    $("#updateProduct").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateProduct(`/products/${$id}`);
    })

    function validateProduct(url) {
        $("#loader").removeClass('error').html('');
        $productCode = $("#productCode").val();
            if($productCode == '') {
                $("#loader").html('Product code is required.').addClass('error');
                $("#productCode").addClass('element-error').focus();
                return;
            }
        $product = $("#product").val();
            if($product == "") {
                $("#loader").html('Product name is required.').addClass('error');
                $("#product").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmProducts").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A product code "${$productCode}" already exists.`);
                return false;
            }
            else {
                if(data === 'saved' || 'updated') {
                    $("#loader").html(`Product ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                    $pageReload = '/products';
                    window.location=$pageReload;
                }
                else{
                    return false;
                }
            }
        })
    }
})