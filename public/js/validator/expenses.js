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


    $('#addMoreExpensesCategory').click(function(event) {
        $addMoreExpenses = '<div class="col-md-4"><div class="form-group">';
        $addMoreExpenses += '<input type="text" name="expenses_description[]" style="width:120px; position:relative; top: 10px; outline: none;border: 1px solid #ccc">';
        $addMoreExpenses += '</div></div>';

        $addMoreExpenses += '<div class="col-md-8"><div class="form-group" style="margin:0; padding:0">';
        $addMoreExpenses += '<input type="number" step="0.01" class="form-control" placeholder="Amount" name="amount[]" id="amount" style="margin:0; border-radius:0">';
        $addMoreExpenses += '</div></div>';
        $('#moreExpenses').append($addMoreExpenses)
    })


    $(document).on('click', '.removeMoreExpenses', function() {
        $(this).parent('row').remove()
    })

})

