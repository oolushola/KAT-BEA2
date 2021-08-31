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

    $('#department').change(function() {
        $('#contentDropper').html('<i class="icon-spinner spinner"></i>Please wait...')
        $.get('/department-expense-type', { department_id: $(this).val() }, function(data) {
            $('#contentDropper').html(data);
        })
    })

    $(document).on("click", "#selectAllLeft", function() {
        const checked = $(this).is(":checked")
        if(checked) {
            $("#selectAllLeftText").html("Deselect all expense types")
            $(".availableExpenseType").attr("checked", "checked")
        }
        else{
            $("#selectAllLeftText").html("Select all expense types")
            $(".availableExpenseType").removeAttr("checked")
        }
    })

    $(document).on('click', '#selectAllRight', function() {
        $checked = $(this).is(':checked');
        if($checked) {
            $('#selectAllRightText').html('Deselect all assigned expense type')
            $('.assignedExpenseType').attr('checked', 'checked')
        }
        else{
            $('#selectAllRightText').html('Select all assigned expense type')
            $('.assignedExpenseType').removeAttr('checked', 'checked')
        }
    })

    $(document).on('click', '#assignExpenseType', function($e) {
        $e.preventDefault();
        $('#department').removeClass('text-danger')
        $department = $('#department').val();
        if($department == 0) {
            $('#department').addClass('text-danger');
            return false;
        }
        $checkedOne = ($('[name="expenseTypes[]"]:checked').length > 0);
        if($checkedOne) {
            $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while expense type is being assigned...').fadeIn(2000).delay(5000).fadeOut(3000)
            $.post('/assign-department-expense-type', $('#frmAssignDepartmentExpense').serializeArray(), function(data) {
                $('#loader').html('')
                $('#contentDropper').html(data)
            })
        }
        else{
            alert('You need to select at least one expense type before assigning.')
            return false
        }
    })

    $(document).on('click', '#removeExpenseType', function($e) {
        $e.preventDefault();
        $('#department').removeClass('text-danger')
        $department = $('#department').val();
        if($department == 0) {
            $('#department').addClass('text-danger');
            return false;
        }
        $checkedOne = ($('[name="assignedExpenseTypes[]"]:checked').length > 0);
        if($checkedOne) {
            $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while this expense type is been remove from department').fadeIn(2000).delay(5000).fadeOut(3000)
            $.post('/remove-assigned-department-expense-type', $('#frmAssignDepartmentExpense').serializeArray(), function(data) {
                $('#loader').html('')
                $('#contentDropper').html(data)
            })
        }
        else {
            alert('You need to select at least one assigned expense type before removing.')
            return false
        }
    })


})

