@extends('layout')

@section('title')Kaya ::. Payment Top Up @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <a href="{{URL('payment-request')}}">
                    <i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Payment Top Up</span>
                </a>
                <input type="text" placeholder="SEARCH" style="font-size:15px; outline:none; padding:3px; font-weight:bold" id="searchPaymentTopUp" />
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
            <!-- Contextual classes -->
        <form id="frmMultipleFullPaymentException" method="POST">
            @csrf            
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-info">
                            <tr style="font-size:11px;">
                                <th width="5%">SN</th>
                                <th>KAID</th>
                                <th>TRUCK NO</th>
                                <th>RATE</th>
                                <th>ADVANCE PAID</th>
                                <th>BALANCE</th>
                                <th>TOP UP WITH</th>
                            </tr>
                        </thead>
                        <tbody class="font-weight-semibold font-size-xs">
                            <?php $count = 0; ?>
                            @if(count($trips))
                                @foreach($trips as $trip)
                                    <?php $count++; ?>
                                    <tr>
                                        <td>{{ $count }}</td>
                                        <td>{{ $trip->trip_id }}</td>
                                        <td>{{ $trip->truck_no }}</td>
                                        <td class="amountPlace">{{ number_format($trip->amount, 2) }}</td>
                                        <td class="advancePlace">{{ number_format($trip->advance, 2) }}</td>
                                        <td class="balancePlace">{{ number_format($trip->balance, 2) }}</td>
                                        <td>
                                            <input name="{{$trip->trip_id}}" id="{{$trip->id}}" type="text" value="" style="font-size:10px; outline:none; width:80px; border:1px solid #ccc" class="topUpValue">
                                            <span id="loader{{$trip->trip_id}}"></span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="table-info">Kaya does not have any trip on journey that requires distress top up</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /contextual classes -->


        </form>
    </div>
</div>

@stop

@section('script')
<script type="text/javascript">
    $(function() {
        $('.topUpValue').keyup(function($e) {
            var value = Number(eval($(this).val()));
            var balance = Number($(this).parent().prev().text().replace(/,/g , ''));
            var advancePaid = Number($(this).parent().prev().prev().text().replace(/,/g , ''));
            var transporterRate = Number($(this).parent().prev().prev().prev().text().replace(/,/g , ''));

            var id = $(this).attr('id');
            var tripId = $(this).attr('name');

            if($e.keyCode === 13) {
                var newAdvance = value + advancePaid
                var newBalance = Number(transporterRate) - Number(newAdvance)
                if(newAdvance >= transporterRate) {
                    $(this).css({ border:'1px solid red' })
                    alert('Operation not allowed. Advance will be more than agreed rate.');
                    return false
                }
                else{
                    $('#loader'+tripId).html('<i class="icon-spinner3 spinner"></i>')
                    $(this).attr('disabled', true)
                    $(this).css({ border: '1px solid #ccc'})
                    $(this).val(value)
                    $event = $(this)
                    $.get('/update-advance-top-up/'+id, { paymentId: id, advance: value }, function(data) {
                        if(data === 'updated') {
                            $('#loader'+tripId).html('<i class="icon-checkmark2"></i>')
                            $($event).parent().prev().prev().html(newAdvance);
                            $($event).parent().prev().html(newBalance)
                            $($event).removeAttr('disabled')

                        }
                        else{
                            return false
                        }
                    })
                }
            }
        })

        
        $("#searchPaymentTopUp").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(`tbody tr`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }); 
            
        

    })
</script>
@stop