$(function() {
  $('#addDepartment').click(function($e) {
      $e.preventDefault();
      $headOfDepartment = $('#headOfDepartment').val();
      if($headOfDepartment == "") {
        $('#loader').html('Head of department is required').addClass('error')
        $('#headOfDepartment').focus();
        return false
      }
      $department = $('#department').val();
      if($department == "") {
        $('#loader').html('Department is required').addClass('error')
        $('#department').focus();
        return false
      }
      $.post('/department', $('#frmDepartment').serializeArray(), function(data) {
          if(data == 'saved') {
              window.location = '';
          }
          else{
              $('#loader').html('This record already exist. Update it instead.').addClass('error')
          }
      })
  })


  $('#updateDepartment').click(function($e) { 
      $e.preventDefault();
      $headOfDepartment = $('#headOfDepartment').val();
      if($headOfDepartment == "") {
        $('#loader').html('Head of department is required').addClass('error')
        $('#headOfDepartment').focus();
        return false
      }
      $department = $('#department').val();
      if($department == "") {
        $('#loader').html('Department is required').addClass('error')
        $('#department').focus();
        return false
      }
      $id = $('#id').val();

      $.post('/department/'+$id, $('#frmDepartment').serializeArray(), function(data) {
        if(data == 'updated') {
            window.location = '/department/';
        }
        else{
            $('#responsePlace').html('This record already exist. Update it instead.').addClass('error')
        }
      })
  })
})

