import $ from 'jquery/dist/jquery';
import {Translation} from "../translations/translation";
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
            this.htmlElement.innerHTML = this.translator.trans("generic.browser.no_gps_available");
        }
    };

    static showPosition (position) {
        console.debug(position);
        $('#plakat_orte_latitude').val(position.coords.latitude);
        $('#plakat_orte_longitude').val(position.coords.longitude);

        $.getJSON(
            `https://nominatim.openstreetmap.org/reverse?lat=${position.coords.latitude}&lon=${position.coords.longitude}&format=json&limit=1&addressdetails=1`,
            function (data) {
                if (data === undefined) {
                    $("#requestErrorModal").modal('toggle');
                }
                const fields = ["city", "town", "village", "state"];
                let city = '';
                let key = 0;
                while (city === '' && key < fields.length) {
                    let field = fields[key];
                    city = data.address[field] || '';
                    key++;
                }
                let address = `${data.address.road} ${data.address.house_number || ''} ${data.address.postcode} ${city}`;
                $('#plakat_orte_address').val(address);
                AddLocation.updateDistrict(data.address);
            }
        ).fail(function () {
            $("#requestErrorModal").modal('toggle');
        });
    };

    getLocation () {
        this.geo_id = navigator.geolocation.watchPosition(
            AddLocation.success,
            AddLocation.error,
            this.options
        );
    };

    static success(pos) {
        AddLocation.showPosition(pos);
    };

    static error (err) {
        console.warn('ERROR(' + err.code + '): ' + err.message);
    };

    static updateDistrict (address) {
        let selectedField = $("#plakat_orte_district");
        let oldValue = selectedField.val();
        if (address.borough === '' || oldValue !== '') {
            return;
        }
        selectedField.val(address.borough);
    };
}
