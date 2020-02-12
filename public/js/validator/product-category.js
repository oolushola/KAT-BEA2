$(function(){
    
    $("#addProductCategory").click(function(e) {
        e.preventDefault();
        validateProductCategory('/product-category');
    });

    $("#updateProductCategory").click(function(e) {
        e.preventDefault();
        $id = $("#id").val();
        validateProductCategory(`/product-category/${$id}`);
    })

    function validateProductCategory(url) {
        $("#loader2").removeClass('error').html('');
        $productCategoryCode = $("#productCategoryCode").val();
            if($productCategoryCode == '') {
                $("#loader2").html('Product category code is required.').addClass('error');
                $("#productCategoryCode").addClass('element-error').focus();
                return;
            }
        $productCategory = $("#productCategory").val();
            if($productCategory == "") {
                $("#loader2").html('Product category is required.').addClass('error');
                $("#productCategory").focus();
                return;
            }
        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $.post(url, $("#frmProductCategory").serializeArray(), function(data) {
            if(data === 'exists') {
                $("#loader").html(`A product category ${$productCategory} already exists.`);
                return false;
            }
            else {
                if(data === 'saved' || 'updated') {
                    $("#loader").html(`Product category has been successfully ${data}.`).addClass('error').fadeIn(3000).fadeOut(5000);
                    setTimeout(function(){
                        $pageReload = '/product-category';
                        window.location=$pageReload;
                    }, 3000);
                }
            }
        })
    }
})