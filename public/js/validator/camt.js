$(function() {
    $('#addAccountTarget').click(function($e) {
        $e.preventDefault();
        $.post('/camt/client-account-target', $('#frmClientTarget').serializeArray(), function(data) {
            if(data === 'added') {
                window.location.href = '';
            }
            else{
                alert('Oops! something went wrong. Contact the Operations Department')
                return false
            }
        })
    })

    $('#accountManager').change(function() {
        $('#contentDropper').html('<i class="icon-spinner spinner"></i>Please wait...')
        $.get('/camt/client-account-manager', { user: $(this).val() }, function(data) {
            $('#contentDropper').html(data);
        })
    })

    $(document).on('click', '#selectAllLeft', function() {
       $checker = $(this).is(':checked')
        if($checker) {
            $('#selectAllLeftText').html('Deselect all clients')
            $('.availableClient').attr('checked', 'checked')
        }
        else{
            $('#selectAllLeftText').html('Select all clients')
            $('.availableClient').removeAttr('checked', 'checked')
        }
    })

    $(document).on('click', '#selectAllRight', function() {
        $checked = $(this).is(':checked');
        if($checked) {
            $('#selectAllRightText').html('Deselect all assigned clients')
            $('.assignedClient').attr('checked', 'checked')
        }
        else{
            $('#selectAllRightText').html('Select all assigned clients')
            $('.assignedClient').removeAttr('checked', 'checked')
        }
    })

    $(document).on('click', '#assignClient', function($e) {
        $e.preventDefault();
        $('#accountManager').removeClass('text-info')
        $user = $('#accountManager').val();
        if($user == 0) {
            $('#accountManager').addClass('text-info');
            return false;
        }
        $checkedOne = ($('[name="clientele[]"]:checked').length > 0);
        if($checkedOne) {
            $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while this client is being assigned...').fadeIn(2000).delay(5000).fadeOut(3000)
            $.post('/assign-client-account-manager', $('#frmAssignAccountManager').serializeArray(), function(data) {
                $('#loader').html('')
                $('#contentDropper').html(data)
            })
        }
        else{
            alert('You need to selecte at least one client before assigning.')
            return false
        }
    })

    $(document).on('click', '#removeClient', function($e) {
        $e.preventDefault();
        $('#accountManager').removeClass('text-info')
        $user = $('#accountManager').val();
        if($user == 0) {
            $('#accountManager').addClass('text-info');
            return false;
        }
        $checkedOne = ($('[name="assignedClientele[]"]:checked').length > 0);
        if($checkedOne) {
            $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while this client is been remove from the account officer...').fadeIn(2000).delay(5000).fadeOut(3000)
            $.post('/remove-assigned-account-manager', $('#frmAssignAccountManager').serializeArray(), function(data) {
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


