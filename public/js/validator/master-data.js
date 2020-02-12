$(function() {
    $("#searchDataset").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#masterDataTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
})