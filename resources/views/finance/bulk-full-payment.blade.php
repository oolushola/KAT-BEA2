@extends('layout')

@section('title')Kaya ::. Target @stop

@section('main')
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <a href="{{URL('payment-request')}}">
                    <i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Full Payments for {{count($trips)}} trips</span>
                </a>
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
                                <th>LOADING SITE</th>
                                <th>DESTINATION</th>
                                <th>RATE(CLIENT)</th>
                                <th>RATE(TRANSPORTER)</th>
                                <th>REMARK</th>
                            </tr>
                        </thead>
                        <tbody class="font-weight-semibold font-size-sm">
                            <?php $count = 0; $sumOfclientRate = 0; $sumOfTransporterRate = 0; ?>
                            @if(count($trips))
                                @foreach($trips as $trip)
                                    <?php
                                        if($count % 2 == 0) { $css = 'table-success'; } else { $css = ''; }
                                        $sumOfclientRate += $trip->client_rate;
                                        $sumOfTransporterRate += $trip->transporter_rate;
                                    ?>
                                    <tr class="{{ $css }}">
                                        <td>{{ $count+=1 }} <input type="hidden" value="{{ $trip->id }}" name="tripPaymentIds[]"></td>
                                        <td>{{ $trip->trip_id }}</td>
                                        <td>{{ $trip->truck_no }}</td>
                                        <td>{{ strtoupper($trip->loading_site) }}</td>
                                        <td>{{ $trip->exact_location_id }}</td>
                                        <td>
                                            <input type="text" class="clientRate" value="{{ $trip->client_rate, 2 }}" name="clientRate[]" style="width:120px; border:1px solid #ccc; outline:none" />
                                        </td>
                                        <td>
                                            <input type="text" class="transporterRate" value="{{ $trip->transporter_rate }}" name="transporterRate[]" style="width:120px; border:1px solid #ccc; outline:none" />
                                        </td>
                                        <td>
                                            <input type="text" name="remark[]" value="@if(isset($trip->remark)){{$trip->remark}}@else Full Payment @endif" style="width:120px; border:1px solid #ccc; outline:none" />
                                        </td>
                                    </tr>
                                @endforeach
                                    <tr>
                                        <td colspan="5">&nbsp;</td>
                                        <td>
                                            Total: ₦{{ number_format($sumOfclientRate, 2) }}
                                        </td>
                                        <td>
                                            Total: ₦{{ number_format($sumOfTransporterRate, 2) }}
                                        </td>
                                        <td>
                                            <button class="btn btn-primary font-weight-semibold font-size-sm" id="updateAll">Update Payment Status</button>
                                            <span id="loader"></span>
                                        </td>
                                    </tr>
                            @else
                                Sorry, nothing has been selected
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
        $('#updateAll').click(function($e) {
            $e.preventDefault();
            $('#loader').html('<i class="icon-spinner3 spinner"></i>Updating...')
            $.post('/update-selected-full-payment', $('#frmMultipleFullPaymentException').serialize(), function(data) {
                if(data == 'updated') {
                    $('#loader').html('<i class="icon-checkmark2"></i>Payment completed.')
                }
            })
        })

        $('.transporterRate').focusout(function() {
            $value = eval($(this).val());
            $(this).blur(function() {
                $(this).val($value)
            })
        })

        $('.clientRate').focusout(function() {
            $value = eval($(this).val());
            $(this).blur(function() {
                $(this).val($value)
            })
        })
    })
</script>
@stop