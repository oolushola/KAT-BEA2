$(function() {
  $("#country").change(function() {
      $country_id = $("#country").val();
      $('#stateHolder').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
      $.get('/getstates', $("#frmClients").serializeArray(), function(data) {
        $("#stateHolder").html(data);
      });
  });

  $("#addClientDetails").click(function(e) {
    e.preventDefault();
    validateClient('/clients')
  });

  $("#updateClientDetails").click(function(e) {
    e.preventDefault();
    $id = $("#id").val();
    validateClient(`/clients/${$id}`);
  });

  $('.parentCompanyStatus').click(function() {
    $value = $(this).attr("value");
    $("#parent_company_status").val($value);
  })

  function validateClient(url) {
    $("#loader").removeClass('error').html('');

    $parent_company_status = $('#parent_company_status').val();
    if($parent_company_status == '') {
      $("#loader").html('Answer the company status question.').addClass('error');
      return false;
    } 
    else if($parent_company_status == 1){
      $parentCompany = $('#parentCompany').val();
      if($parentCompany == '0'){
        $("#loader").html('A parent company is required.').addClass('error');
        $("#parentCompany").focus();
        return false;
      }
    }
    else{
      if($parent_company_status == 0) {
        $parentCompany = $('#parentCompany').val();
        if($parentCompany != '0'){
          $("#loader").html('\'Yes\' is not selected as a parent company').addClass('error');
          return false;
        }
      }
    }
    

    $companyName = $("#companyName").val();
        if($companyName == "") {
            $("#loader").html('Company name is required.').addClass('error');
            $("#companyName").focus();
            return;
        }
    $personOfContact = $("#personOfContact").val();
        if($personOfContact == "") {
            $("#loader").html('Person of contact information is required.').addClass('error');
            $("#personOfContact").focus();
            return;
        }
    $phone_no = $("#phone_no").val();
        if($phone_no == "") {
            $("#loader").html('Client phone number is required.').addClass('error');
            $("#phone_no").focus();
            return;
        }
    $email = $("#email").val();
        if($email == "") {
            $("#loader").html('Email is required.').addClass('error');
            $("#email").focus();
            return;
        }
        else{
          if(validateEmail($email)===false){
            $("#loader").html('Only valid email is required.').addClass('error');
            $("#email").focus();
            return
          }
        }
    $country = $("#country").val();
      if($country == "0") {
          $("#loader").html('Country of operation is required.').addClass('error');
          $("#country").focus();
          return;
      }
    $state = $("#state").val();
      if($state == "0") {
          $("#loader").html('State domiciled is required.').addClass('error');
          $("#country").focus();
          return;
      }
    $address = $("#address").val();
        if($address == "") {
            $("#loader").html('Address is required.').addClass('error');
            $("#address").focus();
            return;
        }
    $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
    $.post(url, $("#frmClients").serializeArray(), function(data) {
        if(data === 'exists') {
            $("#loader").html(`A client with the "${$email}" already exists for kaya.`).addClass('error');
            return false;
        }
        else {
            if(data === 'saved' || 'updated') {
                $("#loader").html(`Loading site ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                  $pageReload = '/clients';
                  window.location=$pageReload;
            }
            else{
                return false;
            }
        }
    })
  }

  function validateEmail(email) {
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      if( !emailReg.test( email ) ) {
        return false;
      }
      else {
        return true;
      }
  }

  $("#closeBtn").click(function(){
    closeBrowserTab();
  })

  function closeBrowserTab() {
    window.top.close();
  }

  $("#addClientProduct").click(function(event) {
    event.preventDefault();
    $productType = $("#productType").val();
    if($productType == "0") {
        $("#loader").html('Product type is required.').addClass('error');
        $("#productType").focus();
        return;
    }
    $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
    $.post('/client-products', $("#frmClientProducts").serializeArray(), function(data) {
        if(data === 'exists') {
            $("#loader").html(`Sorry this product has been added`).addClass('error');
            return false;
        }
        else {
            if(data === 'saved') {
                $("#loader").html(`Product added successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                $pageReload = '';
                window.location=$pageReload;
              }
            else{
                return false;
            }
        }
    })
  });

   $("#addFareRatings").click(function(event) {
     event.preventDefault();
     validateClientFareRatings('/client-fare-rates');
   });

   $("#updateFareRatings").click(function(event) {
    event.preventDefault();
    $id = $("#id").val();
    validateClientFareRatings(`/client-fare-rates/${$id}`);
  });

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

  $("#uploadBulkRating").click(function(e) {
    e.preventDefault();
    $file=$("#file").val();
    if($file == '') {
      $("#loader1").html('A file upload is required.').addClass('error');
      return;
    } 
    else {
      if($file !== '') {
        var ftype = $("#ftype").val();
        validateCsv(ftype);
        var filecheck = $("#filecheck").val();
        if(filecheck == "0"){return;}
      }
    }
    $("#frmFareRatings").submit();
  });

  

  /**
   * 
   * @param {string} url
   * @description validation for fare ratings
   * @return {string}   
   */ 

  function validateClientFareRatings(url) {
    $("#loader").removeClass('error').html('');
    $fromStateId = $("#fromStateId").val();
        if($fromStateId == 0) {
            $("#loader").html('Starting point location is required.').addClass('error');
            $("#fromStateId").focus();
            return;
        }
    $destinationStateId = $("#destinationStateId").val();
        if($destinationStateId == "0") {
            $("#loader").html('Destination state is required.').addClass('error');
            $("#destinationStateId").focus();
            return;
        }
    $destination = $("#destination").val();
        if($destination == "") {
            $("#loader").html('Destination is required.').addClass('error');
            $("#destination").focus();
            return;
        }
    $tonnage = $("#tonnage").val();
      if($tonnage == "") {
          $("#loader").html('Tonnage is required. ').addClass('error');
          $("#tonnage").focus();
          return;
      }
    $amountRate = $("#amountRate").val();
      if($amountRate == "") {
          $("#loader").html('Amount rate is required.').addClass('error');
          $("#amountRate").focus();
          return;
      }
    $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
    $client_name = $("#client_name").val();
    $client_id = $("#client_id").val();
    $.post(url, $("#frmFareRatings").serializeArray(), function(data) {
        if(data === 'exists') {
            $("#loader").html(`This rate already exists for this user.`).addClass('error');
            return false;
        }
        else {
            if(data === 'saved' || 'updated') {
                $("#loader").html(`Amount rate ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                setTimeout(function(){
                    $pageReload = `/client-fare-rates/${$client_name}/${$client_id}`;
                    window.location=$pageReload;
                }, 2000);
            }
            else{
                return false;
            }
        }
    })
  }




  $(document).on("click", "#selectAllLeft", function(){
    $checked = $(this).is(":checked");
    checkAll($checked, '.loadingSiteLeft', '#selectAllLeftText', 'Deselect all available loading sites', 'Select all available loading sites', '#validator');
  });

  $(document).on("click", "#selectAllRight", function(){
    $checked = $(this).is(":checked");
    checkAll($checked, '.loadingSiteRight', '#assignedRightText', 'Deselect all assigned loading sites',
      'Select all assigned loading sites', '#validator');
  });

  function checkAll(check, className, textDescriptorId, textContentSelect, textContentDeselect, validator) {
    if(check) {
      $(className).attr("checked", "checked");
      $(textDescriptorId).html(textContentDeselect);
      $(validator).val(1);
    }
    else {
      $(className).removeAttr("checked");
      $(textDescriptorId).html(textContentSelect);
      $(validator).val(0);
    }
  }

  function singleCheck(check, singleClassName, validatorId) {
    if(check) {
      $(validatorId).val(1);
    }
    else {
      $(validatorId).val(0);
    }
  }

  $(document).on("click", ".loadingSiteLeft", function(){
    $checked = $(this).is(":checked");
    singleCheck($checked, '.loadingSiteLeft', '#validator');
  });

  $(document).on("click", ".loadingSiteRight", function(){
    $checked = $(this).is(":checked");
    singleCheck($checked, '.loadingSiteRight', '#validator');
  });

  $("#state_id").change(function() {
    $('#contentDropper').html('<br><i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
    $.get('/loadingsitesperstate', $("#frmAssignLoadingSite").serializeArray(), function(data) {
      $("#contentDropper").html(data);
    });
  });

  $(document).on("click", "#assignLoadingSite", function(e) {
    e.preventDefault();
    assignment('/store-loading-site', '#frmAssignLoadingSite', '#contentDropper');
  });

  $(document).on("click", "#removeAssignedLoadingSite", function(e) {
    e.preventDefault();
    assignment('/remove-loading-site', '#frmAssignLoadingSite', '#contentDropper');
  });

  function assignment(url, frmname, placeholder) {
    $state_id = $("#state_id").val();
    if($state_id === "0") {
      $("#loader").html(`<i class=\'icon-x\'></i>A location is required before assigning or removing a loading site`).addClass('error');
      return
    }

    $validator = $("#validator").val();
    if($validator === "0") {
      $("#loader").html(`<i class=\'icon-x\'></i> At least 1 loading site needs to be selected before assigning or removing a loading site`).addClass('error');
      return
    }

    $.post(url, $(frmname).serialize(), function(data) {
      $(placeholder).html('<br><i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
      $(placeholder).html(data);
    })
  }

  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });


  $("#frmFareRatings").ajaxForm(function(data) {
    if($data == 'saved'){
      $("#loader").html(`Amount rate ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
      window.location='';
    }
  });

})
