$(function() {
  $("#country").change(function() {
      $country_id = $("#country").val();
      $('#stateHolder').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
      $.get('/getstates', $("#frmClients").serializeArray(), function(data) {
        $("#stateHolder").html(data);
      });
  });

   $("#addTransporterRate").click(function(event) {
     event.preventDefault();
     validateTransporterRate('/transporter-rate');
   });

   $("#updateTransporterRate").click(function(event) {
    event.preventDefault();
    $id = $("#id").val();
    validateTransporterRate(`/transporter-rate/${$id}`);
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
    $("#frmTransporterRate").submit();
  });

  function validateTransporterRate(url) {
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
    $.post(url, $("#frmTransporterRate").serializeArray(), function(data) {
        if(data === 'exists') {
            $("#loader").html(`This rate already exists.`).addClass('error');
            return false;
        }
        else {
            if((data === 'saved') || (data === 'updated')) {
                $("#loader").html(`Amount rate ${data} successfully.`).addClass('error').fadeIn(3000).fadeOut(2000);
                  $pageReload = `/transporter-rate`;
                  window.location=$pageReload;
            }
            else{
                return false;
            }
        }
    })
  }

  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tbody").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });


  $("#frmTransporterRate").ajaxForm(function(data) {alert(data);
    if(data == 'saved'){
      $("#loader").html(`Amount rate ${data} successfully.`).addClass('success');
      window.location='';
    }
  });
})
