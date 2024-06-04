import $ from 'jquery/dist/jquery';
import {Translation} from "../translations/translation";
import {LocationUtils} from "./LocationUtils";
export class AddLocation {
    constructor() {
        this.geo_id= null;
        this.options = {
            enableHighAccuracy: false,
            timeout: 5000,
            maximumAge: 0
        };
        this.htmlElement = null;
        this.translator = new Translation({'locale': 'de'});
    }

    init (div) {
        this.htmlElement = div;
        if (navigator.geolocation) {
            this.getLocation();
        } else {
            this.htmlElement.innerHTML = this.translator.trans("browser.no_gps_available");
        }
    };

    static showPosition (position) {
        $("#election_poster_latitude").val(position.coords.latitude);
        $("#election_poster_longitude").val(position.coords.longitude);
        $.getJSON(
            `https://nominatim.openstreetmap.org/reverse?lat=${position.coords.latitude}&lon=${position.coords.longitude}&format=json&limit=1&addressdetails=1`,
            function (data) {
                if (data === undefined) {
                    $("#requestErrorModal").modal('toggle');
                }
                let locationUtils = new LocationUtils(data.address);
                let city = locationUtils.getCity();
                let address = `${data.address.road} ${data.address.house_number || ''} ${data.address.postcode} ${city}`;
                $('#election_poster_address').val(address);
                locationUtils.updateDistrict('election_poster_district');
                locationUtils.updateCity('election_poster_city');
                locationUtils.updateState('election_poster_state');
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
        });
    };

    getLocation() {
        this.id = navigator.geolocation.watchPosition(
            AddLocation.success,
            AddLocation.error,
            this.options
        );
    };

    stopLocate() {
        navigator.geolocation.clearWatch(this.id);
        this.id = null;
    };

    static success(pos) {
        AddLocation.showPosition(pos);
        $.getJSON(
            `https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`,
            function (data) {
                if (data === undefined || data === '') {
                    return;
                }
                let locationUtils = new LocationUtils(data.address);

                locationUtils.updateCity('election_poster_city');
                let houseNumber = ('house_number' in data.address) ? data.address.house_number : "";

                $('#election_poster_address').val(`${data.address.road} ${houseNumber} ${data.address.postcode} ${locationUtils.getCity()}`);
                locationUtils.updateState('election_poster_state');
                locationUtils.updateDistrict('election_poster_district');
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
        let address = $('#election_poster_address').val();
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
                $('#election_poster_longitude').val(data[0].lon);
                $('#election_poster_latitude').val(data[0].lat);
                let locationUtils = new LocationUtils(data[0].address);

                locationUtils.updateCity('election_poster_city');
                locationUtils.updateState('election_poster_state');
                locationUtils.updateDistrict('election_poster_district');
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
        });
    };

    currentPosition() {
        let latitude = $("#election_poster_latitude");
        let longitude = $("#election_poster_longitude");
        if (longitude.val() !== '' && latitude.val() !== '') {
            AddLocation.success(
                {
                    coords: {
                        longitude: longitude.val(),
                        latitude: latitude.val(),
                    }
                }
            );
        } else {
            navigator.geolocation.getCurrentPosition(
                AddLocation.success,
                AddLocation.error,
                this.options
            );
        }
        if (this.id === null) {
            this.getLocation();
        }
    };
}

let addLocation = new AddLocation();

$(document).ready(function() {
    $("#election_poster_address").on("change", addLocation.updateAddressData);
    $("#election_poster_address").on("input", addLocation.updateAddressData);
    addLocation.init('addressAddFrom');
});