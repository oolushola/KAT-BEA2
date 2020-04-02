<h2>Hi,</h2>
<p style="clear:both; font-size:12px; font-family:tahoma; line-height:20px; display:block">You receieved this notification because you are the one responsible for  monitoring Kaya Africa Technology waybill and offload for our <b>{{$exact_location_id}} trip</b>. Do not hesistate to log onto <a href="{{URL('/')}}">Kaya Africa Technology</a> to give your remark when the offload is completed. Below are the necessary details for your need.

</p>

<h4 style="background:#333; color:#eee; padding:10px; font-family:menlo; margin:0px;">
TRIP UNIQUE ID : {{ $trip_id}}, DESTINATION : {{$exact_location_id}}
</h4>
<p style="font-size:10px; font-family:tahoma">GATED OUT : {{ date('d-m-Y', strtotime($gated_out)) }}</p>
<div class="width:100%; position:relative;">
    <div style="width:50%; float:left; max-height:300px">
        <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px;">CONSIGNEE DETAILS</h5>
        <ul style="margin:10px; padding:0px; list-style:none">
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Customer Name:</b> {{$customers_name}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Customer Phone No:</b> {{$customer_no}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Customer Address:</b> {{$customers_address}}</li>
        </ul>
    </div>
</div>

<div style="width:100%; position:relative;">
    <div style="width:50%; float:right">
        <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px;">DRIVER DETAILS</h5>
        <ul style="margin:10px; padding:0px; list-style:none">
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Driver Name: </b>{{ucfirst($driver_first_name)}} {{ucfirst($driver_last_name)}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Driver Phone No.: </b>{{$driver_phone_number}}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Motor Boy Name: </b>{{$motor_boy_first_name}} {{ $motor_boy_last_name }}</li>
            <li style="font-family:tahoma; font-size:11px; padding:3px;"><b>Motor Boy Phone No.: </b>{{$motor_boy_phone_no}}</li>
        </ul>
    </div>
    
    <div style="clear:both">
        <div style="width:50%; float:left">
            <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px;">TRUCK DETAILS</h5>
                <ul style="margin:10px; padding:0px; list-style:none">
                    <li style="font-family:tahoma; font-size:11px; padding:3px;">
                        <b>TRUCK NO:</b>{{$truck_no}}
                    </li>
                    <li style="font-family:tahoma; font-size:11px; padding:3px;">
                        <b>TONNAGE :</b> {{$tonnage/1000}}T
                    </li>
                    <li style="font-family:tahoma; font-size:11px; padding:3px;">
                        <b>PRODUCT:</b> {{$product}}
                    </li>
                </ul>
        </div>

        <div style="width:50%; float:right">
            <h5 style="background:#eee; color:green; padding:10px; font-family:menlo; text-shadow:1px 1px 1px #fff; margin:0px;">WAYBILL</h5>
            <table>
                <thead style="font-family: tahoma; font-size:11px;">
                    <tr>
                        <th>Sales Order No.</th>
                        <th>Invoice No.</th>
                    </tr>
                </thead>
                <tbody style="font-size:10px; font-family:tahoma; padding-left:20px;">
                    @foreach($waybillDetails as $waybillinfos)
                    <tr>
                        <td>{{$waybillinfos->sales_order_no}}</td>
                        <td>{{$waybillinfos->invoice_no}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<br></br>
<p style="clear:both; font-size:12px; font-family:tahoma; line-height:20px;margin-top:30px">with love from all of us at, <b>KAYA HQ.</b>

</p>



