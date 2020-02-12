$(function() {
    $("#saveDriverTruck").click(function(e) {
        e.preventDefault();
        if (validator()== false) return;
        submitDriverTruck('/assign-driver-truck', '#frmTruckDrivers')
    });

    $("#updateDriverTruck").click(function(e) {
        e.preventDefault();
        $id = $('#id').val();
        if (validator()== false) return;
        submitDriverTruck('/assign-driver-truck/'+$id, '#frmTruckDrivers')
    });


    function validator() {
        $truck_id = $('#truck_id').val();
        if($truck_id == 0) {
            $('#loader').html('Choose a truck').addClass('error');
            return false;
        }
        $driver_id = $('#driver_id').val();
        if($driver_id == 0) {
            $('#loader').html('choose a driver').addClass('error');
            return false;
        }
    }

    function submitDriverTruck(uri, frmName) {
        $.post(uri, $(frmName).serializeArray(), function(data) {
            if(data == 'exist') {
                $('#loader').html('Record exists').addClass('error');
                return false;                
            } else {
                if(data == 'saved' || data == 'updated') {
                    $url = '/assign-driver-truck';
                    window.location = $url;
                }
            }
        });
    }

    $("#bulkUpload").click(function() {
        $(this).css({display:'none'});
        $("#singleUpload").css({display:'inline-block'});
        $("#bulkUploadForm").css({display:'block'})
        $("#singleEntryForm").css({display:'none'});
    });

    $("#singleUpload").click(function() {
        $(this).css({display:'none'});
        $("#bulkUpload").css({display:'inline-block'});
        $("#bulkUploadForm").css({display:'none'})
        $("#singleEntryForm").css({display:'block'});
    });

    $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });


})