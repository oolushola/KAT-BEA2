$(function(){
    
  $("#addAgreement").click(function(e) {
      e.preventDefault();
      validateClientAgreement('/kaya-pay-agreements');
  });

  $("#updateAgreement").click(function(e) {
      e.preventDefault();
      $id = $("#id").val();
      validateClientAgreement(`/kaya-pay-agreements/${$id}`);
  })

  function validateClientAgreement(url) {
      $("#loader").removeClass('error').html('');
      $client = $("#clientId").val();
          if($client == 0) {
              $("#loader").html('Client is required.').addClass('error');
              $("#client").addClass('element-error').focus();
              return;
          }
      $paybackIn = $("#paybackIn").val();
          if($paybackIn == "") {
              $("#loader").html('Payback time is required.   ').addClass('error');
              $("#paybackIn").focus();
              return;
          }
      $interestRate = $("#interestRate").val();
          if($interestRate == "") {
              $("#loader").html('Interest rate is required.').addClass('error');
              $("#interestRate").focus();
              return;
          }
      $overdueCharge = $("#overdueCharge").val();
          if($overdueCharge == "") {
              $("#loader").html('Overdue charge is required.').addClass('error');
              $("#overdueCharge").focus();
              return;
          }
      $('#loader').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
      $.post(url, $("#frmAgreement").serializeArray(), function(data) {
          if(data === 'exists') {
              $("#loader").html(`An agreement already exists for this client.`).addClass('error');
              return false;
          }
          else {
              if(data === 'saved' || data == 'updated') {
                  $("#loader").html(`Client agreement ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                  $pageReload = '/kaya-pay-agreements';
                  window.location=$pageReload;
              }
              else{
                  return false;
              }
          }
      })
  }
})