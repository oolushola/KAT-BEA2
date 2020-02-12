$(function(){
    $('#waybillStatus').change(function(){
        $waybillStatus = $('#waybillStatus').val();
        $uri = '';

        if($waybillStatus == 1){
            $uri = '/healthy-waybill';
        }

        if($waybillStatus == 2) {
            $uri = '/warning-waybill';
        }

        if($waybillStatus == 3) {
           $uri = '/extreme-waybill'; 
        }

        $.get($uri, {waybill_status:$waybillStatus}, function(response){
            $('#contentPlaceholder').html(response);
        })
    })
})