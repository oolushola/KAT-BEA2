@extends('layout')

@section('title') Kaya ::. Request Transporter Payment @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
}
input::placeholder{
    font-size:18px;
    font-weight:bold;
}
</style>
@stop
@section('main')

<div class="page-header page-header-light"> 
    <div class="page-header-content header-elements-md-inline row   ">
        <div class="page-title d-flex col-md-8 paymentCheckRequirement text-primary font-weight-bold" value="1" style="cursor:pointer">
            <h4>
                <i class="icon-coins mr-2"></i>
                <span class="font-weight-semibold">Advance Request</span> &nbsp;
            </h5>
        </div>
        <div class="page-title d-flex col-md-4 paymentCheckRequirement" value="2" style="cursor:pointer">
            <h4>
                <i class="icon-coins mr-2"></i>
                <span class="font-weight-semibold">Balance Request</span> &nbsp;
            </h5>
        </div>
    </div>
</div>

&nbsp;

<input type="text" class="form-control mb-2" placeholder="QUICK SEARCH" id="searchTrip">

<div class="card" id="advancePaymentPlace">
    {!! $advancePaymentRequest->links() !!}
    <form method="POST" id="frmRequestAdvancePayment">
      @csrf
      <div class="row">
          @if(count($advancePaymentRequest))
            <?php $count = 0; ?>
            @foreach($advancePaymentRequest as $advanceRequest)
            <?php $count++;
                if($advanceRequest->tracker == 1) $status = 'Gate In';
                if($advanceRequest->tracker >=2 && $advanceRequest->tracker <=3) $status = 'At the loading bay';
                if($advanceRequest->tracker == 4) $status = 'Departed loading bay';
                if($advanceRequest->tracker >= 5 &&  $advanceRequest->tracker <=6) $status = 'On journey';
                if($advanceRequest->tracker == 7) $status = 'Arrived destination';
                if($advanceRequest->tracker == 8) $status = 'Offloaded';
            ?>
              <section class="col-md-4  mt-2 col-sm-12 col-12 mb-2">
                <div class="card ml-2 pl-2 pt-2 pb-2 mr-2">
                    <div class="table-responsive">
                        <table width="100%">
                            <tbody>
                                <tr>
                                  <td class="font-weight-bold">
                                    Current Status:
                                  </td>
                                  <td class="d-block font-size-sm"> {!! $status !!}</td>
                                </tr>
                                <tr>
                                  <td class="font-weight-bold">Trip ID</td>
                                  <td>
                                      <span class="defaultInfo">
                                          <span class="">{!! $advanceRequest->trip_id !!}</span>  
                                      </span>
                                  </td>
                                </tr>                                        
                                <tr>
                                  <td class="font-weight-bold pt-1">Truck Details
                                  <span class="d-block font-size-sm font-weight-bold text-danger">Destination</span>
                                  <h4 class="text-primary d-block font-weight-bold">{!! $advanceRequest->exact_location_id !!}</h4>
                                  </td>
                                  <td>
                                  <h6 class="mb-0">
                                      <span class="defaultInfo">
                                          <span class="text-primary">{!! $advanceRequest->truck_no !!}</span>
                                          <span class="d-block font-size-sm "><strong>Tonnage</strong>: {!! $advanceRequest->tonnage/1000 !!}T</span>
                                          <span class="d-block font-size-sm "><strong>Product</strong>: {!! $advanceRequest->product !!}</span>
                                      </span>
                                  </h6>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="font-weight-bold" colspan="2">
                                      <span class="d-block font-size-sm text-danger"  style="text-decoration:underline">Transporter Details</span>
                                      <h6 class="mb-0">
                                          <span class="defaultInfo">
                                              <span class="text-primary">{!! $advanceRequest->transporter_name !!}</span>
                                              <span class="d-block font-size-sm "><strong>Phone No:</strong> {!! $advanceRequest->phone_no !!}</span>
                                          </span>
                                      </h6>
                                  </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span>Waybill Details: </span>
                                        @foreach($advanceWaybills as $advanceWaybill)
                                            @if($advanceWaybill->trip_id == $advanceRequest->id)
                                                <span class="badge badge-info pointer">
                                                    <a href="{{URL::asset('assets/img/waybills/'.$advanceWaybill->photo)}}" style="color:#fff" target="_blank">
                                                        {{ $advanceWaybill->sales_order_no }}
                                                    </a>
                                                    <i class="icon-file-check"></i>
                                                </span>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                  <td colspan="2" class="pt-2">
                                    <textarea class="d-block mb-2 form-control" placeholder="Additional Comment" id="advanceRequestComment{{$advanceRequest->id}}">{{$advanceRequest->advance_comment}}</textarea>
                                    @if($advanceRequest->advance_request == TRUE)
                                        <button class="font-weight-bold font-size-sm btn btn-warning" disabled><i class="icon-checkmark2"></i> <i class="hidden">&#x20a6;{!! number_format($advanceRequest->transporter_rate * 0.7, 2) !!}</i> Advance Requested</button>
                                    @else
                                        <button title="&#x20a6;{!! number_format($advanceRequest->transporter_rate * 0.7, 2) !!}" class="btn btn-primary font-weight-bold font-size-sm advanceRequest" id="{{$advanceRequest->id}}" value="{{$advanceRequest->trip_id}}">REQUEST <i class="hidden">&#x20a6;{!! number_format($advanceRequest->transporter_rate * 0.7, 2) !!}</i>ADVANCE</button>
                                        <span id="{{$advanceRequest->trip_id}}"></span>
                                    @endif
                                  </td>
                                </tr> 
                            </tbody>
                        </table>
                    </div>
                </div>
              </section>
            @endforeach
          @else
            <h2>There are no trip available.</h2>
          @endif
      </div>
      <input type="hidden" value="{{Auth::user()->id}}" id="userId">
    </form>
</div>

<!-- Balance request log! -->
<div class="card hidden" id="balancePaymentPlace">
    <div class="row">
        @if(count($allpendingbalanceRequests))
            <?php $count = 0; ?>
            @foreach($allpendingbalanceRequests as $balanceRequest)
            <?php $count++;
                if($balanceRequest->tracker == 1) $status = 'Gate In';
                if($balanceRequest->tracker >=2 && $balanceRequest->tracker <=3) $status = 'At the loading bay';
                if($balanceRequest->tracker == 4) $status = 'Departed loading bay';
                if($balanceRequest->tracker >= 5 &&  $balanceRequest->tracker <=6) $status = 'On journey';
                if($balanceRequest->tracker == 7) $status = 'Arrived destination';
                if($balanceRequest->tracker == 8) $status = 'Offloaded';
            ?>
                <section class="col-md-4  mt-2 col-sm-12 col-12 mb-2">
                <!--<span class="bg-danger font-weight-bold" style="border-radius:100%; padding:15px; margin-left:-20px;">{{ $count }}</span>-->
                    
                    <div class="card ml-2 pl-2 pt-2 pb-2 mr-2">
                        <div class="table-responsive">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td class="font-weight-bold">Trip Details</td>
                                        <td>
                                            <span class="defaultInfo">
                                                <span class="font-weight-bold">{!! $balanceRequest->trip_id !!}</span>  
                                            </span>
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                    <!--    <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">-->
                                    <!--        <span class="d-block">Rate</span>-->
                                    <!--        <h4 class="text-primary d-block font-weight-bold">&#x20a6;{!! number_format($balanceRequest->amount, 2) !!} </h4>-->
                                    <!--        <p>Requested for:  &#x20a6;{!! number_format($balanceRequest->standard_advance_rate, 2) !!}</p>-->
                                    <!--        <p class="text-primary">Amount Paid: &#x20a6;{!! number_format($balanceRequest->advance, 2) !!}</p>-->
                                    <!--    </td>-->
                                    <!--    <td class="text-primary">-->
                                    <!--        <span class="d-block font-size-sm font-weight-bold text-danger">Destination</span>-->
                                    <!--        <h4 class="text-primary d-block font-weight-bold">{!! $balanceRequest->exact_location_id !!}</h4>-->
                                    <!--    </td>-->
                                    <!--<tr>-->
                                    
                                    <tr>
                                        <td class="font-weight-bold mt-2">
                                            <span class="d-block font-size-sm font-weight-bold text-danger">Destination</span>
                                            <h4 class="text-primary d-block font-weight-bold">{!! $balanceRequest->exact_location_id !!}</h4>
                                        </td>
                                        <td>
                                        <h6 class="mb-0">
                                            <span class="defaultInfo">
                                                <span class="text-primary">{!! $balanceRequest->truck_no !!}</span>
                                                <span class="d-block font-size-sm "><strong>Tonnage</strong>: {!! $balanceRequest->tonnage/1000 !!}T</span>
                                                <span class="d-block font-size-sm "><strong>Product</strong>: {!! $balanceRequest->product !!}</span>
                                            </span>
                                        </h6>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold" colspan="2">
                                            <span class="d-block font-size-sm text-danger"  style="text-decoration:underline">Transporter Details</span>
                                            <h6 class="mb-0">
                                                <span class="defaultInfo">
                                                    <span class="text-primary">{!! $balanceRequest->transporter_name !!}</span>
                                                    <span class="d-block font-size-sm "><strong>Phone No:</strong> {!! $balanceRequest->phone_no !!}</span>
                                                </span>
                                            </h6>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td class="font-weight-bold" colspan="2">
                                            <span class="d-block font-size-sm text-danger" style="text-decoration:underline">Bank Details</span>
                                        
                                        <h6 class="mb-0">
                                            <span class="defaultInfo">
                                                <span class="text-primary font-weight-bold">{!! $balanceRequest->account_number !!}</span>
                                                <span class="d-block font-size-sm "><strong>Account Name:</strong>{!! $balanceRequest->account_name !!}</span>
                                                <span class="d-block font-size-sm "><strong>Bank Name</strong>: {!! $balanceRequest->bank_name !!}</span>
                                            </span>
                                        </h6>
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <td>
                                            @if(count($balanceWaybills))
                                                @foreach($balanceWaybills as $collectedWaybill)
                                                    @if($collectedWaybill->trip_id == $balanceRequest->tripid)
                                                        <a class="mr-2 mb-2" href="{{URL('/assets/img/signedwaybills/'.$collectedWaybill->received_waybill)}}" alt="{{$collectedWaybill->received_waybill}}" target="_blank"><i class="icon-file-check"></i></a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if(count($balanceWaybills))
                                                @foreach($balanceWaybills as $collectedWaybill)
                                                    @if($collectedWaybill->trip_id == $balanceRequest->tripid)
                                                         {{ $collectedWaybill->waybill_remark }}
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <form method="POST" class="balanceRequest" id="frm{{$balanceRequest->trip_id}}" enctype="multipart/form-data" action="{{URL('upload-collected-waybill-proof')}}">
                                                @csrf
                                                
                                                <span class="uploadWait">
                                                    <i id="uploadWait{{$balanceRequest->trip_id}}"></i>
                                                </span>
    
                                                <!-- <input type="checkbox" class="uploadWaybillProof" value="{{$balanceRequest->trip_id}}">Upload proof of waybill &nbsp; &nbsp; -->
                                                
                                                <input type="hidden" name="trip_id" value="{{$balanceRequest->tripid}}">
                                                <input type="hidden" name="name" value="{{Auth::user()->first_name}} {{Auth::user()->last_name}}">

                                                <!-- <div id="{{$balanceRequest->trip_id}}" class="hidden moreImagesPanel">
                                                    Show proof of waybill <span class="addMoreProofofWaybill  font-size-sm font-weight-semibold " style="float:right; text-decoration:underline; cursor:pointer">Add more images</span>
                                                    <div>
                                                        <span class="d-block"><input type="file" name="file[]" style="font-size:10px;"></span>
                                                    </div>
                                                    <div class="mt-1">
                                                        <textarea name="remark" class="remark"></textarea>
                                                    </div>
                                                    <button class="btn btn-primary uploadWaybillRemark" value="{{$balanceRequest->trip_id}}"  >Save</button>
                                                </div> -->
    
                                                @if($balanceRequest->balance_request == TRUE)
                                                    <button class="btn btn-warning font-weight-sm font-weight-bold" disabled><i class="icon-checkmark2"></i> Balance <i class="hidden">&#x20a6;{{number_format($balanceRequest->balance, 2)}}</i>Requested</button>
                                                @else
    
                                                <button requester="{{Auth::user()->id}}" class="btn btn-primary font-weight-sm font-weight-bold requestForBalance" title="{{$balanceRequest->trip_id}}" id="{{$balanceRequest->tripid}}">Request <i class="hidden">&#x20a6;{{number_format($balanceRequest->balance, 2)}}</i> Balance</button>
                                                <span class="d-block" class="balanceLoader"></span>
                                                @endif                                            
                                            </form>
                                            
                                        </td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endforeach
        @else
            <h4 class="font-weight-bold text-warning ml-3 mt-2 mr-1">No pending balance request to be made.</h4>
        @endif
    </div>
</div>


&nbsp;



@stop

@section('script')
<script src="{{URL('js/validator/transporter.js')}}" type="text/javascript"></script>
<script type="text/javascript">
    $('.changeRate').dblclick(function() {
        var id = $(this).attr('id');
        var tripId = $(this).attr('value');
        $(`#${id}`).addClass('hidden').removeClass('d-block');
        $(`#updateTr${tripId}`).removeClass('hidden');
        $('.advanceRequest').addClass('hidden');
    });

    $('.updateTrRate').click(function(e) {
        e.preventDefault();
        $tripId = $(this).attr('title')
        $transporterRate = $('#newTrValue'+$tripId).val();
        $('#loader'+$tripId).html('<i class="spinner icon-spinner ml-2"></i> Please wait...');
        $.get('/update-transporter-rate/'+$tripId, { newTrValue: $transporterRate }, function(data) {
            if(data == 'updated') {
                $('#loader').html('<i class="icon-checkmark2"></i> Completed.')
                window.location.href=""
            }
        })
        
    })
</script>
@stop
