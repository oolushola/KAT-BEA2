$(function(){
    
    $('#addIncentives').click((e)=>{
        e.preventDefault();
        validateIncentive('/incentives');
    });

    $('#updateIncentives').click((e)=>{
        e.preventDefault();
        $id = $('#id').val();
        alert($id);
        validateIncentive(`/incentives/${$id}`);
    })

    const validateIncentive = (uri)=> {
        let state, exact_location, incentiveDescription, amount;
        state = $('#state').val();
        if(state == ''){
            response('#responsePlace', 'State is required');
            return false;
        }

        exact_location = $('#exactLocation').val();
        if(exact_location == '0'){
            response('#responsePlace', 'Exact location is required');
            return false;
        }

        incentiveDescription = $('#incentiveDescription').val();
        if(incentiveDescription == ''){
            response('#responsePlace', 'Incentive description is required');
            return false;
        }

        $.post(uri, $('#frmIncentive').serializeArray(), function(response){
            if(data == 'exists'){
                response('#responsePlace', 'Incentive description is required');
                return false;
            }
            else{
                if(data == 'saved') {
                    response('#responsePlace', `Incentive ${response} for ${$exact_location}`);
                    $url = '/incentives';
                    window.location = $url;
                }
            }
        })
        
    }

    const response = (placeHolder, message)=> {
        $(placeHolder).html(`<i class="icon-x"></i>${message}`).fadeIn(3000).delay(2000).fadeOut(2000).addClass('error');
    }

    $('#state').change(()=>{
        let stateid = $('#state').val();
        $('#exactLocationPlaceholder').html('<i class="icon-spinner spinner "></i> Loading...');
        $.get('/exact-destination', {state_id:stateid}, function(data){
            $('#exactLocationPlaceholder').html(data);
        })
    })


});