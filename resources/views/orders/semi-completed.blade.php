@extends('layout')

@section('title')Payment Request @stop

@section('css')
<style type="text/css">
th {
    white-space: nowrap;
}
td{
    white-space: nowrap;
    padding: 5px;
}
</style>
@stop

@section('main')

<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex text-info">
            <h4><i class="icon-truck mr-2"></i> <span class="font-weight-bold font-size-sm ">OFFLOADED, BUT NOT DROPPED OFF </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>

<div class="row" id="advancecarrier">
    <div class="col-md-12">
    &nbsp;

        <div class="card p-2">
            <div class="row">
                @if(count($semicompletedTrips))
                <?php $advanceIterator = 1; ?>
                    @foreach($semicompletedTrips as $key => $trip)
                        <section class="col-md-3 mt-2 col-sm-12 col-12 mb-2">
                            
                            <div class="card">
                                <div class="table-responsive">
                                    <table class="" width="100%">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <span class="defaultInfo">
                                                        <span class="font-weight-bold">{!! $trip->trip_id !!}</span> 
                                                    </span>
                                                </td>
                                                <td>{!! $trip->transporter_name !!}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-primary"><span class="d-block font-size-sm font-weight-bold text-danger">Destination</span>
                                                <p class="text-primary d-block font-weight-bold">{!! $trip->exact_location_id !!}</p>
                                                </td>
                                                <td>
                                                    <h6 class="mb-0">
                                                        <span class="defaultInfo">
                                                            <span class="text-primary">{!! $trip->truck_no !!}</span>
                                                            <span class="d-block font-size-sm "><strong>Tonnage</strong>: {!! $trip->tonnage/1000 !!}T, 
                                                                {!! $trip->loaded_weight !!}
                                                            </span>
                                                            <span class="d-block font-size-sm "><strong>Product</strong>: {!! $trip->product !!}</span>
                                                            <span class="d-block font-size-sm "><strong>Weight</strong>: {!! $trip->loaded_weight !!}</span>
                                                        </span>
                                                    </h6>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="2">Drv: {{ $trip->driver_first_name }}, {{ $trip->driver_phone_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span class="lastKnownLocation" id="{{ $trip->id }}">{{ $trip->last_known_location }}</span>
                                                    <input type="text" value="{{ $trip->last_known_location }}" style="width:100px; border:1px solid #ccc; outline:none " class="d-none"  id="lastKnownLocationInput{{ $trip->id }}" />
                                                </td>
                                                <td class="font-size-sm font-weight-bold" style="font-size:11px;">
                                                    <button class="btn btn-primary font-size-xs font-weight-bold deliveredDropOff" id="{{ $trip->id }}">DELIVERED</button>
                                                    <span id="loader{{ $trip->id }}"></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    @endforeach
                @else
                    <p class="ml-3 mb-3 font-weight-bold text-danger">You are currently not expecting any container drop off (Please escalate if this is not right.)</p>
                @endif
            </div>
            
        </div>
    </div>
</div>

@stop


@section('script')
<script type="text/javascript">
    $(function() {
        $('.lastKnownLocation').dblclick(function() {
            $event = $(this);
            $id = $(this).attr('id');
            $(this).addClass('d-none');
            $('#lastKnownLocationInput'+$id).removeClass('d-none')

            $('#lastKnownLocationInput'+$id).keyup(function($e) {
                if($e.keyCode === 27) {
                    $(this).addClass('d-none')
                    $event.removeClass('d-none')
                }
                else{
                    if($e.keyCode === 13) {
                        $lastKnownLocation = $(this).val()
                        $('#loader'+$id).html('<i class="icon-spinner3 spinner "></i>')
                        $.get('/update-semi-trip-location', { trip_id: $id, location: $lastKnownLocation }, function(data) {
                            if(data === 'updated') {
                                $('#loader'+$id).html('<i class="icon-checkmark3"></i>').fadeIn(2000).delay(5000).fadeOut(3000);
                                $event.html($lastKnownLocation).removeClass('d-none')
                                $('#lastKnownLocationInput'+$id).addClass('d-none')
                            }
                            else{
                                return false;
                            }
                        })
                    }
                }
            })
        })

        $('.deliveredDropOff').click(function() {
            $id = $(this).attr('id');
            $('#loader'+$id).html('<i class="icon-spinner3 spinner "></i>')
            $.get('/drop-off-completed', { id: $id }, function(data) {
                if(data === 'delivered') {
                    window.location = '';
                }
                else {
                    return false;
                }
            })
        })
    })
</script>
@stop
