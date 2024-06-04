import $ from 'jquery/dist/jquery';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap';
import 'leaflet/dist/leaflet.css';
import 'leaflet';
import 'leaflet.markercluster';
import 'leaflet.featuregroup.subgroup';
import {Place} from '../plakate/Place';
import '../../css/app.css';
import iconPathWish from '../../images/wish_place.png';
import {Translation} from "../translations/translation";

let translator = new Translation({'locale': 'de'});

let map = L.map('map').setView([52.516215, 13.3922187], 11);

let url = `/api/data/places/${state}/${city}`;
if (typeof wahllokale !== 'undefined' && wahllokale === true) {
    url = `/api/data/places_wahllokale/${state}/${city}`;
}
$.get(url, function (data) {
    $('.loader').remove();

    let places = data.posters;
    let wahllokaldata = typeof data.wahllokale !== 'undefined' ?  data.wahllokale : [];
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
        minZoom: 1,
    }).addTo(map);

    let place = new Place(
        L.markerClusterGroup(
            {
                maxClusterRadius: 40,
                iconCreateFunction: function(cluster) {
                    return L.divIcon({ html: '<span>' + cluster.getChildCount() + '</span>', iconSize: [32, 32], className: 'mycluster' });
                },
                polygonOptions: {
                    color: "grey"
                },
            }
        ),
        map,
        L.control.layers(null, null, { collapsed: true })
    );
    place.placesCollection = places;
    place.parteiIcon = L.icon({
        iconSize: [32, 32],
        iconUrl: '/favicon.png'
    });
    place.parteiIconWish = L.icon({
        iconSize: [32, 44],
        iconUrl: iconPathWish
    });
    if (wahllokaldata.length > 0) {
        place.wahllokaleCollection(wahllokaldata);
    }
    place.generate();

    $(".leaflet-control-layers-overlays").prepend(`<b>${translator.trans('map.districts')}</b>`);
}).fail(function () {
    alert(translator.trans('error.no-data-load'));
});
