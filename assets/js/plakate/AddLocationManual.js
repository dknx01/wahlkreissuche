import $ from 'jquery/dist/jquery';
import {LocationUtils} from './LocationUtils';
import {Translation} from "../translations/translation";

export class AddLocationManual {

    constructor () {
        this.translator = new Translation({'locale': 'de'});
        if (!navigator.geolocation) {
            $("#manualAddress")
                .append(this.translator.trans("browser.no_gps_available"))
                .show();
        }
    };

    updateAddressData() {
        let address = $('#election_poster_manual_address').val();
        if(address === '' || address.length <= 5) {
            return;
        }
        $.getJSON(
            "https://nominatim.openstreetmap.org/?q="+encodeURI(address)+"&format=json&limit=1&addressdetails=1",
            function (data) {
                if (data[0] === undefined) {
                    $("#requestErrorModal").modal('toggle');
                }
                $('#election_poster_manual_longitude').val(data[0].lon);
                $('#election_poster_manual_latitude').val(data[0].lat);
                let locationUtils = new LocationUtils(data[0].address);
                locationUtils.updateState('election_poster_manual_state');
                locationUtils.updateCity('election_poster_manual_city');
                locationUtils.updateDistrict('election_poster_manual_district');
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
        });
    };
}

$(document).ready(function() {
    let addLocationManual = new AddLocationManual();
    $("#election_poster_manual_address").on("change", addLocationManual.updateAddressData);
});