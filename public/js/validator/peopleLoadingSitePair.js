$(function() {
  $('#person').change(function() {
      $('#contentDropper').html('<i class="icon-spinner spinner"></i>Please wait...')
      $.get('/loading-site-person-pair', { person: $(this).val() }, function(data) {
          $('#contentDropper').html(data);
      })
  })

  $(document).on('click', '#selectAllLeft', function() {
     $checker = $(this).is(':checked')
      if($checker) {
          $('#selectAllLeftText').html('Deselect all loading sites')
          $('.availableLoadingSite').attr('checked', 'checked')
      }
      else{
          $('#selectAllLeftText').html('Select all loading sites')
          $('.availableLoadingSite').removeAttr('checked', 'checked')
      }
  })

  $(document).on('click', '#selectAllRight', function() {
      $checked = $(this).is(':checked');
      if($checked) {
          $('#selectAllRightText').html('Deselect all assigned loading sites')
          $('.pairedLoadingSite').attr('checked', 'checked')
      }
      else{
          $('#selectAllRightText').html('Select all assigned loading sites')
          $('.pairedLoadingSite').removeAttr('checked', 'checked')
      }
  })

  $(document).on('click', '#assignLoadingSite', function($e) {
      $e.preventDefault();
      $('#person').removeClass('text-info')
      $user = $('#person').val();
      if($user == 0) {
          $('#person').addClass('text-info');
          return false;
      }
      $checkedOne = ($('[name="loadingSites[]"]:checked').length > 0);
      if($checkedOne) {
          $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while loading sites are being assigned...').fadeIn(2000).delay(5000).fadeOut(3000)
          $.post('/pair-person-loading-site', $('#frmPairOpsLoadingSite').serializeArray(), function(data) {
              $('#loader').html('')
              $('#contentDropper').html(data)
          })
      }
      else{
          alert('You need to selecte at least one loading site before pairing.')
          return false
      }
  })

  $(document).on('click', '#removeLoadingSite', function($e) {
      $e.preventDefault();
      $('#person').removeClass('text-info')
      $user = $('#person').val();
      if($user == 0) {
          $('#person').addClass('text-info');
          return false;
      }
      $checkedOne = ($('[name="pairedLoadingSites[]"]:checked').length > 0);
      if($checkedOne) {
          $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while loading site(s) is/are been remove from person').fadeIn(2000).delay(5000).fadeOut(3000)
          $.post('/remove-paired-loading-site', $('#frmPairOpsLoadingSite').serializeArray(), function(data) {
              $('#loader').html('')
              $('#contentDropper').html(data)
          })
      }
      else {
          alert('You need to select at least one assigned client before removing.')
          return false
      }
   })

})


