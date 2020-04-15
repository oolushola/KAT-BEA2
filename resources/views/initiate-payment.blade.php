<h4 style="background:#333; color:#eee; padding:10px; font-family:menlo; margin:0px;">
Advance Request for TRIP : {{$tripid}} 
</h4>
<p style="font-size:10px; font-family:tahoma">Payment Initiated: {{date('Y-m-d, g:i A')}}</p>
<div class="width:100%; position:relative">
    <div style="width:50%; float:left">
        <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px;">TRIP DETAILS</h5>
        <ul style="margin:10px; padding:0px; list-style:none">
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Customer Name:</b> {{$customer_name}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Product:</b> {{$product_name}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Location:</b> {{$destination}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Tonnage:</b> {{$tonnage/1000}}<sub>t</sub></li>
        </ul>
    </div>

    
</div>

<div style="clear:both" style="width:100%">
    <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px; border-bottom:1px solid #ccc">
    <span style="color:#000; text-shadow:1px 1px 1px #fff">Transporter's Name:- </span>{{$transporter_name}}</h5>
</div>

<div style="width:100%; position:relative">
    <div style="width:50%; float:left">
        <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px;">ACCOUNT DETAILS</h5>
        <ul style="margin:10px; padding:0px; list-style:none">
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Bank Name: </b>{{$bank_name}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Account Name: </b>{{$account_name}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Account Number: </b>{{$account_number}}</li>
        </ul>
    </div>
    <div style="width:50%; float:left">
        <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px;">PAYMENT SUMMARY</h5>
            <ul style="margin:10px; padding:0px; list-style:none">
                <li style="font-family:tahoma; font-size:11px; padding:3px;">
                    <b>Kaya Balance:</b> &#x20A6;{{number_format($current_balance, 2)}}
                </li>
                <li style="font-family:tahoma; font-size:11px; padding:3px;">
                    <b>Advance :</b> &#x20A6;{{number_format($standardAdvanceRate, 2)}}
                </li>
                <li style="font-family:tahoma; font-size:11px; padding:3px;">
                    <b>Amount Payable / Deductable:</b> &#x20A6;{{number_format($amountPayable,2)}}
                </li>
                <li style="font-family:tahoma; font-size:11px; padding:3px;">
                    <b>Kaya Balance after Advance Deduction:</b> &#x20A6;{{number_format($available_balance, 2)}}
                </li>
            </ul>
    </div>
</div>
