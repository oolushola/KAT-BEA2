$(function() {

    $("#closeBtn").click(function(){
      closeBrowserTab();
    })

    function closeBrowserTab() {
      window.top.close();
    }
    $("#user_id").change(function() {
      $('#contentDropper').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
      $.get('/user-assigned-states', $("#frmUserAssignedRegion").serializeArray(), function(data) {
        $("#contentDropper").html(data);
      });
    });

    $("#regional_state_id").change(function() {
        $('#contentDropper').html('<i class="icon-spinner2 spinner mr-2"></i>Please Wait...').addClass('error');
        $.get('/user-assigned-states', $("#frmUserAssignedRegion").serializeArray(), function(data) {
          $("#contentDropper").html(data);
        });
    });

  

    $(document).on("click", "#selectAllLeft", function(){
      $checked = $(this).is(":checked");
      checkAll($checked, '.exactLocationLeft', '#selectAllLeftText', 'Select all available state', 'Deselect all available states', '#validator');
    });
  
    $(document).on("click", "#selectAllRight", function(){
      $checked = $(this).is(":checked");
      checkAll($checked, '.exactLocationRight', '#assignedRightText', 'Select all assigned states',
        'Deselect all assigned states', '#validator');
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
  
    $(document).on("click", ".exactLocationLeft", function(){
      $checked = $(this).is(":checked");
      singleCheck($checked, '.exactLocationLeft', '#validator');
    });
  
    $(document).on("click", ".exactLocationRight", function(){
      $checked = $(this).is(":checked");
      singleCheck($checked, '.exactLocationRight', '#validator');
    });
  
    
  
    $(document).on("click", "#assignLocations", function(e) {
      e.preventDefault();
      assignment('/assign-adhoc-clerk-region', '#frmUserAssignedRegion', '#contentDropper');
    });
  
    $(document).on("click", "#removeAssignedLocations", function(e) {
      e.preventDefault();
      assignment('/remove-adhoc-clerk-region', '#frmUserAssignedRegion', '#contentDropper');
    });
  
    function assignment(url, frmname, placeholder) {
      $regional_state_id = $("#regional_state_id").val();
      if($regional_state_id === "0") {
        $("#loader").html(`<i class=\'icon-x\'></i>Choose a region you wish to add / remove a location from`).addClass('error');
        return
      }
      
      $user_id = $("#user_id").val();
      if($user_id === "0") {
        $("#loader").html(`<i class=\'icon-x\'></i>Select a user you wish to assign or remove a region from `).addClass('error');
        return
      }
  
      $validator = $("#validator").val();
      if($validator === "0") {
        $("#loader").html(`<i class=\'icon-x\'></i> At least 1 region needs to be selected before assigning or removing a state `).addClass('error');
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
  
  
    
  
  })
  