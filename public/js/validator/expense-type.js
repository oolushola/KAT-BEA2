$(function() {
  $('#addExpenseType').click(function($e) {
      $e.preventDefault();
      $expenseType = $('#expenseType').val();
      if($expenseType == "") {
          $('#loader').html('Expense type is required').addClass('error')
          $('#expenseType').focus();
          return false
      }
      $.post('/expense-type', $('#frmExpenseType').serializeArray(), function(data) {
          if(data == 'saved') {
              window.location = '';
          }
          else{
              $('#loader').html('This record already exist. Update it instead.').addClass('error')
          }
      })
  })


  $('#updateExpenseType').click(function($e) { 
      $e.preventDefault();
      $expenseType = $('#expenseType').val();
      if($expenseType == "") {
        $('#loader').html('Expense type is required').addClass('error')
        $('#expenseType').focus();
        return false
      }
      $id = $('#id').val();
      
      $.post('/expense-type/'+$id, $('#frmExpenseType').serializeArray(), function(data) {
          if(data == 'updated') {
              window.location = '/expense-type/';
          }
          else{
              $('#responsePlace').html('This record already exist. Update it instead.').addClass('error')
          }
      })
  })
})

