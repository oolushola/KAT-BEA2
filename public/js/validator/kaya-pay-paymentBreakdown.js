$(function(){
  $("#addPaymentBreakdown").click(function(e) {
      e.preventDefault();
      $(this).attr("disabled", "disabled")
      const evt = $(this)
      validatePaymentBreakdown("/kaya-pay/payment-breakdown", evt);
  });

  $("#updatePaymentBreakdown").click(function(e) {
      e.preventDefault();
      $id = $("#id").val();
      $(this).attr("disabled", "disabled")
      const evt = $(this)
      validatePaymentBreakdown(`/kaya-pay/payment-breakdown/${$id}`, evt);
  })

  function validatePaymentBreakdown(url, evt) {
      $("#loader").removeClass('error').html('');

      $clientId = $("#clientId").val();
      if($clientId == '0') {
        $("#loader").html('Client is required.').addClass('error');
        $("#clientId").addClass('element-error').focus();
        return;
      }

      $loadingSite = $("#loadingSite").val();
      if($loadingSite == '') {
        $("#loader").html('Loading site is required.').addClass('error');
        $("#loadingSite").addClass('element-error').focus();
        return;
      }
      $gatedIn = $("#gatedIn").val();
      if($gatedIn == '') {
          $("#loader").html('Date of gate in is required.').addClass('error');
          $("#gatedIn").addClass('element-error').focus();
          return;
      }
      $truckNo = $("#truckNo").val();
      if($truckNo == '') {
        $("#loader").html('Truck no is required.').addClass('error');
        $("#truckNo").addClass('element-error').focus();
        return;
      }
      $destinationState = $("#destinationState").val();
      if($destinationState == '') {
        $("#loader").html('Destination state is required.').addClass('error');
        $("#destinationState").addClass('element-error').focus();
        return 
      }
      $destinationCity = $("#destinationCity").val();
      if($destinationCity == '') {
        $("#loader").html('Destination city is required.').addClass('error');
        $("#destinationCity").addClass('element-error').focus();
        return 
      }

      $atLoadingBay = $("#atLoadingBay").val();
      if($atLoadingBay == '') {
        $("#loader").html('At loading bay is required.').addClass('error');
        $("#atLoadingBay").addClass('element-error').focus();
        return 
      }

      $paymentDisbursed = $("#paymentDisbursed").val();
      if($paymentDisbursed == '') {
        $("#loader").html('Date payment disbursed is required.').addClass('error');
        $("#atLoadingBay").addClass('element-error').focus();
        return 
      }

      $waybillNo = $("#waybillNo").val();
      if($waybillNo == '') {
        $("#loader").html('Waybill no is required.').addClass('error');
        $("#waybillNo").addClass('element-error').focus();
        return 
      }

      $loadedWeight = $("#loadedWeight").val();
      if($loadedWeight == '') {
        $("#loader").html('Loaded weight is required.').addClass('error');
        $("#loadedWeight").addClass('element-error').focus();
        return 
      }
      $payoutRate = $("#payoutRate").val();
      if($payoutRate == '') {
        $("#loader").html('Payout rate is required.').addClass('error');
        $("#payoutRate").addClass('element-error').focus();
        return 
      }
      $atLoadingBay = $("#atLoadingBay").val();
      if($atLoadingBay == '') {
        $("#loader").html('Loading bay date is required.').addClass('error');
        $("#atLoadingBay").addClass('element-error').focus();
        return 
      }

      $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...');
      $e = $(this)
      $.post(url, $('#frmPaymentBreakdown').serializeArray(), function(data) {
        if(data === 'exists') {
            $("#loader").html('A payment with the same client and waybill no already exists.').addClass('error');
            evt.removeAttr("disabled", "disabled")
            return 
        }  
        else { 
            if(data === 'saved' || data === 'updated') {
                $("#loader").html('payment breakdown '+data+' successfully').addClass('error');
                window.location = '/kaya-pay/payment-breakdown';
            }
        }
      })
  }

  $("#uploadBulkRating").click(function($e) {
    $e.preventDefault();
    $(this).attr("disabled", "disabled")
    $("#frmBulkPaymentBreakdown").submit();
  })


  $('#frmBulkPaymentBreakdown').ajaxForm(function(data){
      if(data == 'exists'){
        $("#loader").html('Record already '+data).addClass('error');
        return false;
      }
      else{
          if(data === 'populated') {
            $("#loader").html('payment breakdown '+data+' successfully').addClass('error');
            window.location = '/kaya-pay/payment-breakdown';
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


   $('#searchBox').on("keyup", function() {
      var value = $('#searchBox').val().toLowerCase()
      $("tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      }); 
   });

});