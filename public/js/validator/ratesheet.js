$(function () {

  $("#newRate").click(function () {
    $("#frmRateSheet")[0].reset()
    $("#addRateSheet").removeClass("d-none")
    $("#updateRateSheet").addClass("d-none")
  })

  function responder(msg) {
    return $("#loader").html(msg).fadeIn(1000).delay(3000).fadeOut(3000).addClass("text-danger mr-2")
  }

  $("#addRateSheet").click(function (e) {
    e.preventDefault()

    $client = $("#client").val();
    if ($client === "") {
      responder("<i class='icon-x'></i>Client is required.")
      $("#client").addClass("text-danger")
      return false
    }
    $state = $("#state").val();
    if ($state === "") {
      responder("<i class='icon-x'></i>Choose the state of Ax.")
      $("#state").addClass("text-danger")
      return false
    }
    $exactDestination = $("#exactDestination").val()
    if ($exactDestination === "") {
      responder("<i class='icon-x'></i>Exact destination is required.")
      $("#exactDestination").addClass("text-danger")
      return false
    }
    $clientRate = $("#clientRate").val()
    if ($clientRate === "") {
      responder("<i class='icon-x'></i>Client rate is required.")
      $("#clientRate").addClass("text-danger")
      return false
    }
    $transporterRate = $("#transporterRate").val()
    if ($transporterRate === "") {
      responder("<i class='icon-x'></i>Transporter rate rate is required.")
      $("#transporterRate").addClass("text-danger")
      return false
    }
    $tonnage = $("#tonnage").val()
    if ($tonnage === "") {
      responder("<i class='icon-x'></i>Tonnage is required.")
      $("#tonnage").addClass("text-danger")
      return false
    }
    $percentageMarkUpDiff = $clientRate - $transporterRate
    $percentageMarkUp = ($percentageMarkUpDiff / $clientRate) * 100;
    console.log($percentageMarkUp);
    if ($percentageMarkUp < 8) {
      responder("The percentage margin shouldn't be less than 8%").fadeIn(1000).delay(3000).fadeOut(3000)
      return false
    }
    $(this).attr("disabled", "disabled").text("Please wait...")
    $comp = $(this)
    $.post('/rate-sheet', $("#frmRateSheet").serialize(), function (data) {
      if (data === 'exists') {
        $comp.removeAttr("disabled").text("Add to Rate Sheet")
        responder("A rate with the destination, tonnage and selected client already exists.")
        return false
      }
      else {
        if (data === "saved") {
          $comp.removeAttr("disabled").text("Add to Rate Sheet")
          responder("Rate has been successfully added to sheets")
          window.location.href = '';
        }
      }
    })
  })

  $('#showBulkRate').click(function () {
    if ($("#rateSheetToggler").val() == 0) {
      $("#bulkUploadForm").removeClass("d-none")
      $("#singleEntryForm").addClass("d-none")
      $("#rateSheetToggler").val(1)
    }
    else {
      $("#bulkUploadForm").addClass("d-none")
      $("#singleEntryForm").removeClass("d-none")
      $("#rateSheetToggler").val(0)
    }
  })

  $(".updateRate").click(function () {
    $rateId = $(this).attr("id")
    $("#updateSpinner").removeClass("d-none")
    $.get('/rate-sheet/' + $rateId, function (data) {
      $("#id").val($rateId)
      $("#client").val(data.client_id)
      $("#state").val(data.state)
      $("#exactDestination").val(data.exact_location)
      $("#clientRate").val(data.client_rate)
      $("#transporterRate").val(data.transporter_rate)
      $("#tonnage").val(data.tonnage)
      $("#updateSpinner").addClass("d-none")

      $("#addRateSheet").addClass("d-none")
      $("#updateRateSheet").removeClass("d-none")
    })
  })


  $("#updateRateSheet").click(function (e) {
    e.preventDefault()

    $client = $("#client").val();
    if ($client === "") {
      responder("<i class='icon-x'></i>Client is required.")
      $("#client").addClass("text-danger")
      return false
    }
    $state = $("#state").val();
    if ($state === "") {
      responder("<i class='icon-x'></i>Choose the state of Ax.")
      $("#state").addClass("text-danger")
      return false
    }
    $exactDestination = $("#exactDestination").val()
    if ($exactDestination === "") {
      responder("<i class='icon-x'></i>Exact destination is required.")
      $("#exactDestination").addClass("text-danger")
      return false
    }
    $clientRate = $("#clientRate").val()
    if ($clientRate === "") {
      responder("<i class='icon-x'></i>Client rate is required.")
      $("#clientRate").addClass("text-danger")
      return false
    }
    $transporterRate = $("#transporterRate").val()
    if ($transporterRate === "") {
      responder("<i class='icon-x'></i>Transporter rate rate is required.")
      $("#transporterRate").addClass("text-danger")
      return false
    }
    $tonnage = $("#tonnage").val()
    if ($tonnage === "") {
      responder("<i class='icon-x'></i>Tonnage is required.")
      $("#tonnage").addClass("text-danger")
      return false
    }
    $id = $("#id").val();
    $(this).attr("disabled", "disabled").text("Please wait...")
    $comp = $(this)

    $.ajax({
      method: 'PATCH',
      url: '/rate-sheet/' + $id,
      data: $('#frmRateSheet').serialize(),
      error: function (err) {
        responder(`${err.status}: ${err.statusText}`)
        $comp.removeAttr("disabled").text("Update Rate")
      },
      success: function (data) {
        $comp.removeAttr("disabled").text("Update Rate")
        responder("Rate updated successfully.")
        window.location.href = '';
      },

    })



  })

  $("#uploadBulkRateSheet").click(function (e) {
    e.preventDefault()
    $("#frmBulkRateSheet").submit()
  })

  $("#frmBulkRateSheet").ajaxForm(function (data) {
    if (data === "updated") {
      window.location.href = '';
    }
  })


})