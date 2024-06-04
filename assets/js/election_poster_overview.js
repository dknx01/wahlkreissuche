import $ from "jquery";
$(document).ready(function(){
    $("#user_overview_search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#election_poster_overview tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});