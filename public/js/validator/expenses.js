$(function() {
    $('#addExpenses').click(function($e) {
        $e.preventDefault();
        $year = $('#year').val();
        if($year == 0) {
            $('#responsePlace').html('Year is required.').addClass('error')
            $('#year').focus();
            return false
        }
        $month = $('#month').val();
        if($month == 0) {
            $('#responsePlace').html('Month is required.').addClass('error')
            $('#year').focus();
            return false
        }
        $amount = $('#amount').val();
        if($amount == "") {
            $('#responsePlace').html('Amount is required').addClass('error')
            $('#amount').focus();
            return false
        }
        else{
           $('#expensesAmount').val(eval($amount))
        }
        
        $.post('/other-expenses', $('#frmOtherExpenses').serializeArray(), function(data) {
            if(data == 'saved') {
                window.location = '';
            }
            else{
                $('#responsePlace').html('This record already exist. Update it instead.').addClass('error')
            }
        })
    })


    $('#updateExpenses').click(function($e) { 
        $e.preventDefault();
        $year = $('#year').val();
        if($year == 0) {
            $('#responsePlace').html('Year is required.').addClass('error')
            $('#year').focus();
            return false
        }
        $month = $('#month').val();
        if($month == 0) {
            $('#responsePlace').html('Month is required.').addClass('error')
            $('#year').focus();
            return false
        }
        $amount = $('#amount').val();
        if($amount == "") {
            $('#responsePlace').html('Amount is required').addClass('error')
            $('#amount').focus();
            return false
        }
        else{
           $('#expensesAmount').val(eval($amount))
        }
        
        $id = $('#id').val();

        $.post('/other-expenses/'+$id, $('#frmOtherExpenses').serializeArray(), function(data) {
            if(data == 'updated') {
                window.location = '/other-expenses/';
            }
            else{
                $('#responsePlace').html('This record already exist. Update it instead.').addClass('error')
            }
        })
    })


})

