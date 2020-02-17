<?php
function timeDifference($gatedIn, $timeArrivedLoadingBay){
    if($gatedIn && $timeArrivedLoadingBay != '') {
        $mydate1 = new DateTime($gatedIn);
        $mydate2 = new DateTime($timeArrivedLoadingBay);
        $interval = $mydate1->diff($mydate2);
        $elapsed = $interval->format('%a days %h hours %i minutes');
    }
    else{
        $elapsed = '';
    }
    return $elapsed;
}
function eventdetails($arrayRecord, $master, $field){
    foreach($arrayRecord as $object) {
        if($master->id == $object->trip_id) {
            if(($field == 'location_check_one' && $field!='')){
                echo date('Y-m-d, g:i A', strtotime($object->$field));
            }
            elseif(($field == 'location_check_two' && $object->$field!='')) {
                echo date('Y-m-d, g:i A', strtotime($object->$field));
            }
            elseif(($field == 'time_arrived_destination' && $object->$field!='')) {
                echo date('Y-m-d, g:i A', strtotime($object->$field));
            }
            elseif(($field == 'offload_start_time' && $object->$field!='')){
                echo date('Y-m-d, g:i A', strtotime($object->$field));
            }
            elseif(($field == 'offload_end_time' && $object->$field!='')){
                echo date('Y-m-d, g:i A', strtotime($object->$field));
            }
            else{
                echo $object->$field;
            }
            break;
        }
    }
}

function transactionDetails($arrayRecord, $master, $field) {
    foreach($arrayRecord as $object) {
        if(($master->client_id == $object->client_id) && ($master->exact_location_id == $object->id)) {
            return number_format($object->$field, 2);
        }
    }
}

function grossMargin($arrayRecord, $master, $field) {
    foreach($arrayRecord as $object) {
        if(($master->client_id == $object->client_id) && ($master->exact_location_id == $object->id)) {
            return number_format($object->amount_rate - $object->transporter_amount_rate, 2);
        }
    }
}

function percentageMarkup($arrayRecord, $master, $field) {
    foreach($arrayRecord as $object) {
        if(($master->client_id == $object->client_id) && ($master->exact_location_id == $object->id)) {
           $grossMargin = $object->amount_rate - $object->transporter_amount_rate;
           $transporterRate = $object->transporter_amount_rate;
           $markup = ($grossMargin / $transporterRate) * 100;
           return number_format($markup, 2);
        }
    }
}

function percentageMargin($arrayRecord, $master, $field) {
    foreach($arrayRecord as $object) {
        if(($master->client_id == $object->client_id) && ($master->exact_location_id == $object->id)) {
           $grossMargin = $object->amount_rate - $object->transporter_amount_rate;
           $clientRate = $object->amount_rate;
           $margin = ($grossMargin / $clientRate) * 100;
           return number_format($margin, 2);
        }
    }
}


function trippayment($arrayRecord, $master, $field, $checker) {
    foreach($arrayRecord as $payment) {
        if(($payment->trip_id == $master->id) && ($payment->$checker == TRUE)) {
            return $answer = $payment->$field;
        }
    }
}

function totalPayout($arrayRecord, $master, $advance, $balance) {
    $checkone = 0.00;
    $checktwo = 0.00;
    foreach($arrayRecord as $payment) {
        if($payment->trip_id === $master->id) {
            if($payment->advance_paid == true && $payment->balance_paid == false){
                return '&#x20a6;'.number_format($calculate = $payment->$advance, 2);
            }
            elseif($payment->advance_paid == true && $payment->balance_paid == true){
                $calculate = $payment->$advance + $payment->$balance;
                return '&#x20a6;'.number_format($calculate = $payment->$advance + $payment->balance, 2); 
            }
            // else{
            //     return '&#x20a6;'.number_format($calculate = $checkone + $checktwo, 2);
            // }
        }
    }
}

function exceptionRemarks($arrayRecord, $master, $field) {
    foreach($arrayRecord as $object) {
        if($object->trip_id == $master->id) {
            return $object->$field;
        }
    }
}
?>