$(function() {
    $("#gateInContainer").removeClass('hidden').addClass("show");
    //click function for a specific view
    $(".tripStatus").click(function() {
        $checked = $(this).attr("value");
       
        if($checked == 1) {
            $("#gateInContainer").removeClass('hidden').addClass("show");
            $("#loadingBayArrivalContainer").addClass('hidden');
            $("#loadingContainer").addClass("hidden");
            $("#DepartureContainer").addClass("hidden");
            $("#gateOutContainer").addClass("hidden");
            $("#tracker").val($checked);
        }
        if($checked == 2) {
            $("#gateInContainer").addClass("hidden");
            $("#loadingBayArrivalContainer").removeClass('hidden').addClass('show');
            $("#loadingContainer").addClass("hidden");
            $("#DepartureContainer").addClass("hidden");
            $("#gateOutContainer").addClass("hidden");
            $("#tracker").val($checked);
        }

        if($checked == 3) {
            $("#gateInContainer").addClass("hidden");
            $("#loadingBayArrivalContainer").addClass('hidden');
            $("#loadingContainer").removeClass('hidden').addClass('show');
            $("#DepartureContainer").addClass("hidden");
            $("#gateOutContainer").addClass("hidden");
            $("#tracker").val($checked);
        }

        if($checked == 4) {
            $("#gateInContainer").addClass("hidden");
            $("#loadingBayArrivalContainer").addClass('hidden');
            $("#loadingContainer").addClass('hidden');
            $("#DepartureContainer").removeClass('hidden').addClass('show');
            $("#gateOutContainer").addClass("hidden");
            $("#tracker").val($checked);
        }

        if($checked == 5) {
            $("#gateInContainer").addClass("hidden");
            $("#loadingBayArrivalContainer").addClass('hidden');
            $("#loadingContainer").addClass('hidden');
            $("#DepartureContainer").addClass('hidden');
            $("#gateOutContainer").removeClass('hidden').addClass('show');
            $("#tracker").val($checked);
        }
    })
})