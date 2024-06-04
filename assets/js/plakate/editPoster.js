import $ from "jquery";
import select2 from 'select2/dist/js/select2.min';
require ('select2/dist/js/i18n/de');
// create global $ and jQuery variables
global.$ = global.jQuery = $;

$(document).ready(function(){
    $('#election_poster_district').select2({
        tags: true,
        language: 'de'
    });
    $('#election_poster_city').select2({
        tags: true,
        language: 'de'
    });
});

