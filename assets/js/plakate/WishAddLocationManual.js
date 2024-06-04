import $ from 'jquery/dist/jquery';
import {LocationUtils} from "./LocationUtils";

export class WishAddLocationManual {

    constructor () {
        if (!navigator.geolocation) {
            $("#manualAddress")
                .append("Geolocation not supported by browser")
                .show();
        }
    };

    updateAddressData() {
        let address = $('#wish_election_poster_manual_address').val();
        if(address === '' || address.length <= 5) {
            return;
        }
        $.getJSON(
            "https://nominatim.openstreetmap.org/?q="+encodeURI(address)+"&format=json&limit=1&addressdetails=1",
            function (data) {
                if (data[0] === undefined) {
                    $("#requestErrorModal").modal('toggle');
                }
                $('#wish_election_poster_manual_longitude').val(data[0].lon);
                $('#wish_election_poster_manual_latitude').val(data[0].lat);
                let locationUtils = new LocationUtils(data[0].address);
                locationUtils.updateState('wish_election_poster_manual_state');
                locationUtils.updateCity('wish_election_poster_manual_city');
                locationUtils.updateDistrict('wish_election_poster_manual_district');
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
        });
    };
}

$(document).ready(function() {
    let addLocationManual = new WishAddLocationManual();
    $("#wish_election_poster_manual_address").on("change", addLocationManual.updateAddressData);
});