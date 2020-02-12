$(function(){


    $("#basciInfoTab").click(function() {
        $(this).addClass("tabs-default")
        $("#bankTab").addClass('tabs').removeClass('tabs-default');
        $("#guarantorTab").addClass('tabs').removeClass('tabs-default');
        $("#nextofkinTab").addClass("tabs").removeClass('tabs-default');

        $(".basicInfoDetails").addClass("show").removeClass('hidden');
        $(".bankDetails").addClass("hidden");
        $(".guarantorDetails").addClass("hidden");
        $(".nextOfKinDetails").addClass("hidden");  
    });

    $("#bankTab").click(function() {
        $(this).addClass("tabs-default"); 
        $("#basciInfoTab").addClass("tabs").removeClass('tabs-default');
        $("#guarantorTab").addClass('tabs').removeClass('tabs-default');;
        $("#nextofkinTab").addClass('tabs').removeClass('tabs-default');;

        $(".basicInfoDetails").addClass("hidden").removeClass("show");
        $(".bankDetails").addClass("show").removeClass("hidden");
        $(".guarantorDetails").addClass("hidden");
        $(".nextOfKinDetails").addClass("hidden");
    });

    $("#guarantorTab").click(function() {
        $(this).addClass("tabs-default")
        $("#basciInfoTab").addClass("tabs").removeClass('tabs-default');
        $("#bankTab").addClass('tabs').removeClass('tabs-default');
        $("#nextofkinTab").addClass('tabs').removeClass('tabs-default');

        $(".basicInfoDetails").addClass("hidden").removeClass("show");
        $(".bankDetails").addClass("hidden");
        $(".guarantorDetails").addClass("show").removeClass("hidden");
        $(".nextOfKinDetails").addClass("hidden");
    })

    $("#nextofkinTab").click(function() {
        $(this).addClass("tabs-default")
        $("#basciInfoTab").addClass("tabs").removeClass('tabs-default');
        $("#bankTab").addClass('tabs').removeClass('tabs-default');
        $("#guarantorTab").addClass('tabs').removeClass('tabs-default');

        $(".basicInfoDetails").addClass("hidden");
        $(".bankDetails").addClass("hidden");
        $(".guarantorDetails").addClass("hidden");
        $(".nextOfKinDetails").addClass("show").removeClass('hidden');
    })




    $("#addTransporter").click(function(e) {
        e.preventDefault();
        validateTransporter();
    });

    $("#updateTransporter").click(function(e) {
        e.preventDefault();
        validateTransporter();
    })

    function validateTransporter() {
        $("#loader").removeClass('error').html('');
        $transporterName = $("#transporterName").val();
            if($transporterName == '') {
                $("#loader").html('Transporter Name is required.').addClass('error');
                $("#transporterName").addClass('element-error').focus();
                return;
            }
        $email = $("#email").val();
            if($email == "") {
                $("#loader").html('Transporter email is required.').addClass('error');
                $("#email").focus();
                return;
            }
            else {
                if(validateEmail($email)===false) {
                    $("#loader").html('Only valid email is required.').addClass('error');
                    $("#email").focus();
                    return;
                }
            }
        $phoneNumber = $("#phoneNumber").val();
            if($phoneNumber == '') {
                $("#loader").html('Phone number is required.').addClass('error');
                $("#phoneNumber").addClass('element-error').focus();
                return;
            }
        $address = $("#address").val();
            if($address == '') {
                $("#loader").html('Address is required.').addClass('error');
                $("#address").addClass('element-error').focus();
                return;
            }
        $bankName = $("#bankName").val();
        if($bankName == '') {
            $("#loader").html('Which bank does this tranporter uses? Click on the Bank Details Tab.').addClass('error');
            $("#bankName").addClass('element-error').focus();
            return 
        }
        $accountName = $("#accountName").val();
        if($accountName == '') {
            $("#loader").html('Bank account name is required.').addClass('error');
            $("#accountName").addClass('element-error').focus();
            return 
        }

        $accountNumber = $("#accountNumber").val();
        if($accountNumber == '') {
            $("#loader").html('Account No. of this transporter is required.').addClass('error');
            $("#accountNumber").addClass('element-error').focus();
            return 
        }


        $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
        $('#frmTransporter').submit();
    }


    $('#frmTransporter').ajaxForm(function(data){
        if(data == 'exists'){
            $("#loader").html('Record already '+data).addClass('error');
            return false;
        }
        else{
            if(data === 'saved' || data === 'updated') {
                $("#loader").html('Transporter details '+data+' successfully').addClass('error');
                window.location = '/transporters';
            }
        }
    })


    function validateEmail(email) {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if( !emailReg.test( email ) ) {
        return false;
      }
      else {
        return true;
      }
    }

    $("#bulkUpload").click(function() {
        $(this).css({display:'none'});
        $("#singleUpload").css({display:'inline-block'});
        $("#bulkUploadForm").css({display:'block'})
        $("#singleEntryForm").css({display:'none'});
    });

    $("#singleUpload").click(function() {
        $(this).css({display:'none'});
        $("#bulkUpload").css({display:'inline-block'});
        $("#bulkUploadForm").css({display:'none'})
        $("#singleEntryForm").css({display:'block'});
    });


    $('#addmore').click(function(){
        $document = '<div class="form-group row">';
        $document+='<input type="file" name="document[]" class="col-md-5" style="font-size:10px; top:5px;">';
        $document+='<input type="text" class="" name="description[]" placeholder="Description">';
        $document+='<span style="margin-top:5px; color:red; cursor:pointer; font-size:10px; font-weight:bold; font-family:tahoma" id="removeInput">Remove</span>';
        $document+='</div>'
        $('#moreDocuments').append($document);
    });

    $(document).on('click', '#removeInput', function(){
        $(this).parent('div').remove();
    });


    $(document).on('click', '#changeDocument', function(){
        $(this).prev('.description').prev('input').removeAttr('disabled');
        $(this).prev('input').removeAttr('disabled');
        $(this).addClass(['icon-unlocked', 'lockDocument', 'text-success']).removeClass('icon-lock5');
    });

    $(document).on('click', '.lockDocument', function() {
        $(this).prev('.description').prev('input').attr('disabled', 'disabled');
        $(this).prev('input').attr('disabled', 'disabled');
        $(this).addClass('icon-lock5').removeClass(['icon-unlocked', 'lockDocument', 'text-success']);
    });

    //delete a specific document
    $(document).on('click', '.deleteDocument', function() {
        $id = $(this).attr('id');
        $description = $(this).attr('name');
        $ask = confirm(`Are you sure you want to delete ${$description}`);
        if($ask){
            $.post(`/delete-transporters-document/${$id}`, $('#frmTransporter').serializeArray(), function(data) {
                if(data == 'deleted') {
                    alert(`${$description} has been deleted successfully.`);
                    window.location.href = ''
                }
            })
        }
        else{
            return false;
        }
    })

    


});