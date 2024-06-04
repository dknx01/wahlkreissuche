import $ from "jquery";
$(document).ready(function(){
    $("#user_overview_search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#user_overview tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});