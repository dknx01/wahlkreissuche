import $ from 'jquery/dist/jquery';
import {LocationUtils} from "./LocationUtils";

export class WishAddLocation {
    constructor() {
        this.geo_id= null;
        this.options = {
            enableHighAccuracy: false,
            timeout: 5000,
            maximumAge: 0
        };
        this.htmlElement = null;
    }

    init (div) {
        this.htmlElement = div;
        if (navigator.geolocation) {
            this.getLocation();
        } else {
            this.htmlElement.innerHTML = "Geolocation not supported by browser";
        }
    };

    static showPosition (position) {
        $("#wish_election_poster_latitude").val(position.coords.latitude);
        $("#wish_election_poster_longitude").val(position.coords.longitude);
        $.getJSON(
            `https://nominatim.openstreetmap.org/reverse?lat=${position.coords.latitude}&lon=${position.coords.longitude}&format=json&limit=1&addressdetails=1`,
            function (data) {
                if (data === undefined) {
                    $("#requestErrorModal").modal('toggle');
                }
                let locationUtils = new LocationUtils(data.address);

                let address = `${data.address.road} ${data.address.house_number || ''} ${data.address.postcode} ${locationUtils.getCity()}`;
                $('#wish_election_poster_address').val(address);

                locationUtils.updateDistrict('wish_election_poster_district');
                locationUtils.updateCity('wish_election_poster_city');
                locationUtils.updateState('wish_election_poster_state');
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
        });
    };

    getLocation() {
        this.id = navigator.geolocation.watchPosition(
            WishAddLocation.success,
            WishAddLocation.error,
            this.options
        );
    };

    stopLocate() {
        navigator.geolocation.clearWatch(this.id);
        this.id = null;
    };

    static success(pos) {
        WishAddLocation.showPosition(pos);
        $.getJSON(
            `https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`,
            function (data) {
                if (data === undefined) {
                    return;
                }
                let locationUtils = new LocationUtils(data.address);

                locationUtils.updateCity('wish_election_poster_city');

                $('#wish_election_poster_address').val(`${data.address.road} ${data.address.house_number} ${data.address.postcode} ${locationUtils.getCity()}`);
                $("#wish_election_poster_state").val(data.address.state);
                locationUtils.updateState( 'wish_election_poster_state');
                locationUtils.updateDistrict('wish_election_poster_district');
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
            addLocation.stopLocate();
        });
    };

    static error(err) {
        console.warn('ERROR(' + err.code + '): ' + err.message);
    };

    updateAddressData() {
        let address = $('#wish_election_poster_address').val();
        if(address === '') {
            addLocation.currentPosition();
            return;
        }
        addLocation.stopLocate();
        $.getJSON(
            "https://nominatim.openstreetmap.org/?q="+encodeURI(address)+"&format=json&limit=1&addressdetails=1",
            function (data) {
                if (data[0] === undefined) {
                    return;
                }
                $('#wish_election_poster_longitude').val(data[0].lon);
                $('#wish_election_poster_latitude').val(data[0].lat);
                let locationUtils = new LocationUtils(data.address);

                locationUtils.updateDistrict('wish_election_poster_district');
                locationUtils.updateCity('wish_election_poster_city');
                locationUtils.updateState('wish_election_poster_state');
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
        });
    };

    currentPosition() {
        let latitude = $("#wish_election_poster_latitude");
        let longitude = $("#wish_election_poster_longitude");
        if (longitude.val() !== '' && latitude.val() !== '') {
            WishAddLocation.success(
                {
                    coords: {
                        longitude: longitude.val(),
                        latitude: latitude.val(),
                    }
                }
            );
        } else {
            navigator.geolocation.getCurrentPosition(
                WishAddLocation.success,
                WishAddLocation.error,
                this.options
            );
        }
        if (this.id === null) {
            this.getLocation();
        }
    };
}

let addLocation = new WishAddLocation();

$(document).ready(function() {
    $("#wish_election_poster_address").on("change", addLocation.updateAddressData);
    $("#wish_election_poster_address").on("input", addLocation.updateAddressData);
    addLocation.init('addressAddFrom');
});
