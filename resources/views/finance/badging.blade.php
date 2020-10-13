@extends('layout')

@section('title') Kaya ::. Assign ad-hoc staff to a region @stop

@section('main')
<form name="frmBadging" id="frmBadging" method="POST">
    @csrf
    <div class="page-header page-header-light">
        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex">
                <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">TRUCK BADGING</span></h4>
                <a href="#" class="header-elements-toggle text-default d-md-none">
                    <i class="icon-more"></i>
                </a>
                <input type="text" id="searchTrips" class="ml-3 font-weight-semibold font-size-sm" placeholder="SEARCH TRIPS" style="outline:none; padding:3px;"  />
            </div>
        </div>
    </div>

    <input type="hidden" value="0" id="validator">
    <p class="m-0 mt-2" id='loader'></p>
    <div id="contentDropper">
        <div class="row">
            <div class="col-md-5">
            &nbsp;

                <div class="card" >
                    <div class="table-responsive" style="max-height:600px">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold" colspan="4">AVAILABLE TRIPS</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%">
                                        <input type="checkbox" id="selectAllLeft">
                                    </td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllLeftText">
                                        Select all available trips
                                    </td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="badgeAndAvailableTrips">
                                @if(count($availableTrips))
                                <?php $count = 0; ?>
                                @foreach($availableTrips as $key => $trip)

                                <?php $count++; if($count % 2 == 0) { $cssStyle = 'table-success'; } else { $cssStyle = ''; } ?>
                                    <tr class="{{ $cssStyle }}">
                                        <td>
                                            <input type="checkbox" value="{{ $trip->id }}" class="availableTrips" name="availableTrucks[]" />
                                        </td>
                                        <td>{{ $trip->trip_id }}</td>
                                        <td>{{ $trip->truck_no }}</td>
                                        <td>{{ $trip->exact_location_id }}</td>
                                    </tr>
                                @endforeach
                                @else
                                <tr class="table-success" style="font-size:10px">
                                    <td colspan="2" class="font-weight-semibold">You do not have any trips to badge</td>
                                </tr>
                                @endif
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
            &nbsp;
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary font-weight-bold font-size-xs" id="badgeTruck">BADGED
                        <i class="icon-point-right ml-2"></i>
                    </button>
                    <br /><br />
                    <button type="submit" class="btn btn-danger font-weight-bold font-size-xs" id="removeBadgedTruck">REMOVE <i class="icon-point-left ml-2"></i></button>
                </div>
            </div>

            <div class="col-md-5">
            &nbsp;

                <!-- Contextual classes -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td class="table-primary font-weight-bold" colspan="4">BADGED TRIPS</td>
                                </tr>
                                <tr>
                                    <td class="table-info" width="10%"><input type="checkbox" id="selectAllRight"></td>
                                    <td class="table-info font-weight-semibold" colspan="4" id="selectAllRightText">Select all badged trips</td>
                                </tr>
                            </thead>
                            <tbody style="font-size:10px;" class="badgeAndAvailableTrips">
                                @if(count($badgedTrips))
                                <?php $counter = 0; ?>
                                @foreach($badgedTrips as $key => $badgeTrip)
                                <?php $counter++; if($counter % 2 == 0) { $css = 'table-success'; } else { $css = ''; } ?>
                                    <tr class="{{ $css }}">
                                        <td><input type="checkbox" class="badgedTrips" name="badgedTrips[]" value="{{ $badgeTrip->id }}" /></td>
                                        <td>{{ $badgeTrip->trip_id }}</td>
                                        <td>{{ $badgeTrip->truck_no }}</td>
                                        <td>{{ $badgeTrip->exact_location_id }}</td>
                                    </tr>
                                @endforeach
                                @else
                                <tr class="table-success" style="font-size:10px">
                                    <td colspan="2" class="font-weight-semibold">You've not baddged in any trip yet.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /contextual classes -->


            </div>

            
        </div>
    </div>
</form>
@stop

@section('script')
<script type="text/javascript">
    $(document).on('click', '#selectAllLeft', function() {
        $checked = $(this).is(':checked');
        if($checked) {
            $('#selectAllLeftText').html('Deselect all available trips')
            $('.availableTrips').attr('checked', 'checked')
        }
        else{
            $('#selectAllLeftText').html('Select all available trips')
            $('.availableTrips').removeAttr('checked', 'checked')
        }
    })

    $(document).on('click', '#selectAllRight', function() {
        $checked = $(this).is(':checked');
        if($checked) {
            $('#selectAllRightText').html('Deselect all badged trips')
            $('.badgedTrips').attr('checked', 'checked')
        }
        else{
            $('#selectAllRightText').html('Select all badged trips')
            $('.badgedTrips').removeAttr('checked', 'checked')
        }
    })

    $(document).on('click', '#badgeTruck', function($e) {
        $e.preventDefault();
        $checkedOne = ($('[name="availableTrucks[]"]:checked').length > 0);
        if($checkedOne) {
            $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while this truck is being badged...').fadeIn(2000).delay(5000).fadeOut(3000)
            $.post('/badge-truck', $('#frmBadging').serializeArray(), function(data) {
                $('#loader').html('')
                $('#contentDropper').html(data)
            })
        }
        else{
            alert('You need to selecte at least one trip before badging.')
            return false
        }
    })

    $(document).on('click', '#removeBadgedTruck', function($e) {
       $e.preventDefault();
        $('#loader').html('<i class="icon-spinner spinner"></i>Please wait, while this truck is been remove from badge...').fadeIn(2000).delay(5000).fadeOut(3000)
        $.post('/remove-badge-truck', $('#frmBadging').serializeArray(), function(data) {
            $('#loader').html('')
            $('#contentDropper').html(data)
        })
    })

    //autosearch("keyup", "#searchTrips", ".badgeAndAvailableTrips")

    
        
        $('#searchTrips').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $(`.badgeAndAvailableTrips tr`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    


</script>
@stop