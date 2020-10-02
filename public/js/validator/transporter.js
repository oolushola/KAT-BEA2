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

        $asssignUserId = $("#asssignUserId").val();
            if($asssignUserId == '') {
                $("#loader").html('Choose who is responsible for this transporter').addClass('error');
                $("#asssignUserId").addClass('element-error').focus();
                return;
            }

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
    });

    $('.paymentCheckRequirement').click(function(){
        $('.paymentCheckRequirement').removeClass('text-primary');
        $checker = $(this).attr("value");
        if($checker == 1) {
            $('#advancePaymentPlace').addClass('show').removeClass('hidden')
            $('#balancePaymentPlace').addClass('hidden').removeClass('show');
            $(this).addClass('text-primary');
        } else {
            if($checker == 2) {
                $('#advancePaymentPlace').addClass('hidden').removeClass('show')
                $('#balancePaymentPlace').addClass('show').removeClass('hidden');
                $(this).addClass('text-primary');
            }
        }
        
    })


    /**
     * Request for advance payment
     */

     $(".advanceRequest").click(function($event){
         $e = $(this);
         $event.preventDefault();
         $value = $(this).attr("value");
         $specificTripId = $(this).attr('id')
         $title = $(this).attr("title")
         $requester = $("#userId").val();
         $.get('/advance-request-payment', {trip_id:$specificTripId, user_id:$requester}, function(data){
            if(data == 'blackListed') {
            alert('Sorry, you can not request for this transporter payment until onboarding is completed.')
            return false
            } 
            else {
                $(`#${$value}`).html('<i class="spinner icon-spinner2"></i>Please wait, notifying finance...').addClass('font-size-sm');
                if(data == 'requestSent') {
                    $e.html(`<i class="icon-checkmark2"></i>${$title} <br> Advance Requested`).addClass('btn btn-warning').prop("disabled", "disabled");
                    $(`#${$value}`).html('').removeClass('font-size-sm');
                }
                else{
                    $(`#${$value}`).html('<i class="spinner icon-spinner2"></i>Oops! something went wrong.').addClass('font-size-sm text-danger');
                }
            }
         })
     });


     $('.addMoreProofofWaybill').click(function(){
        $fileName = '<span class="d-block mt-1" style="font-size:10px;"><input type="file" name="file[]"></span>' 
        $(this).next().append($fileName);
     })

     $('.uploadWaybillProof').click(function(){
         $checked = $(this).is(":checked");
         $value = $(this).attr("value");
         if($checked){
            $(`#${$value}`).removeClass("hidden");
            $(this).next().next().next().next().addClass("hidden");
         }
         else{
            $(`#${$value}`).addClass("hidden");
            $(this).next().next().next().next().removeClass("hidden");
         }
     })

     // Request for balance
     $(".requestForBalance").click(function($event){
        $e = $(this);
        $event.preventDefault();
        $value = $(this).attr("value");
        $specificTripId = $(this).attr('id')
        $title = $(this).attr("title")
        $requester = $(this).attr("requester");

        $.get('/balance-request-payment', {trip_id:$specificTripId, user_id:$requester}, function(data){
            if(data === 'blackListed') {
                alert('Sorry, you can not request for this transporter payment until onboarding is completed.')
                return false
            }
            else{
                $e.html('<i class="spinner icon-spinner2"></i>Please wait, <br>notifying finance...').addClass('font-size-sm');
                if(data == 'requestSent') {
                $e.html(`<i class="icon-checkmark2"></i>Balance of ${$title} <br> Requested`).addClass('btn btn-warning').prop("disabled", "disabled");
                //$(`#${$value}`).html('').removeClass('font-size-sm');
                }
                else{
                    if(data == 'abort') {
                        $e.html('<i class="icon-x"></i> Request Aborted').addClass('btn btn-danger').prop('disabled', 'disabled'); 
                        $e.next().html('You haven\'t uploaded any proof of waybill.').addClass('font-size-sm mt-1 text-danger font-weight-bold').fadeIn(2000).delay(5000).fadeOut('1000');     
                    }
                }
            }
        })
    });

    //change account details;
    $(".changeAccountDetails").click(function() {
        $title = $(this).attr("title");
        $(`#bankDetailsDefault${$title}`).addClass("hidden")
        $(`#bankInformation${$title}`).removeClass("hidden")
        $(this).addClass("hidden");
        $(this).next($('.closeAccountDetails')).removeClass('hidden');
        $(".advanceRequest").attr('disabled', 'disabled');
    })
    //close account details
    $('.closeAccountDetails').click(function() {
        $title = $(this).attr("title");
        $(`#bankDetailsDefault${$title}`).removeClass("hidden")
        $(`#bankInformation${$title}`).addClass("hidden")
        $(this).addClass("hidden");
        $(this).prev($('.changeAccountDetails')).removeClass('hidden');
        $(".advanceRequest").removeAttr('disabled');
    })

    //Update account details
    $('.updateTransporterAccount').click(function($e){
        $e.preventDefault();
        $uniqueIdentifier = $(this).attr("title");
        $tractor =  'bankInformation'+$uniqueIdentifier;
        $accountName = $('#'+$tractor).find('.accountName').val();
        $bankName = $('#'+$tractor).find('.bankName').val();
        $accountNumber = $('#'+$tractor).find('.accountNumber').val();
        $id = $(this).attr("value");
        
        $(this).attr("disabled", "disabled").html('Updating...')
        $.get(`/transporter-account-update/${$id}`, {accountName:$accountName, bankName:$bankName, accountNumber:$accountNumber}, function(data) {
            if(data == 'updated') {
                window.location = '/request-transporter-payment';
            }
            else{
                alert('Connection Lost. If problem persist, please contact tech support.');
                return false;
            }
        });
    })

    //Quick search trips
    //Search Trips!
    $("#searchTrip").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".card section").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });


    $('.uploadWaybillRemark').click(function($event){
         $event.preventDefault();
         $tripId = $(this).attr("value");
         $frmName = `#frm${$tripId}`;
         $('#uploadWait'+$tripId).html('<i class="icon-spinner3 spinner"></i>')
         $($frmName).submit();
    });

    $(".balanceRequest").ajaxForm(function(data){
         if(data == "updated"){
             $('.moreImagesPanel').addClass('hidden')
             $('.requestForBalance').removeClass('hidden')
             $('.uploadWaybillProof').prop('checked', false)
             $('.uploadWait').addClass('hidden')
         }
         else{
             return false;
         }
    })

    $('#allTransporter').on("change", function() {
        var value = $(this).val().toLowerCase();
        $("tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
    })


     $('#searchBox').on("keyup", function() {
        var value = $('#searchBox').val().toLowerCase()
        $("tbody tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        }); 
     });


     $(".transporterStatus").click(function() {
         $transporterId = $(this).attr("id");
         $placeholder = $('#status'+$transporterId)
         $($placeholder).html('<i class="spinner icon-spinner2"></i>')
         $this = $(this)
         $.get('/toggle-transporter-status', {id: $transporterId}, function(data) {
             $($this).html('<i class="icon-checkmark2"></i>'+ data)
         })
     })


});